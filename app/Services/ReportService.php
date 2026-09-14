<?php

namespace App\Services;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Models\PropertyMaster;
use App\Models\PropertySectionMapping;
use App\Models\SplitedPropertyDetail;
use App\Models\Section;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class ReportService
{

    protected $propertyMasterService;

    public function __construct()
    {
        $pms = new PropertyMasterService();
        $this->propertyMasterService = $pms;
    }
    public function tabularRecord()
    {
        //Total property id's created
        $totalNazulProperties = PropertyMaster::where('land_type', '45')->count();
        $totalRehabilitationProperties = PropertyMaster::where('land_type', '46')->count();
        $totalId = PropertyMaster::count();
        $totalFreeHoldLeaseHold = PropertyMaster::whereIn('status', [951, 952])->count();
        $freeHoldProperties = PropertyMaster::where('status', 952)->count();
        $leaseHoldProperties = PropertyMaster::where('status', 951)->count();

        //Section wise breakup of data
        $propertiesBySection = PropertyMaster::select('section_code', DB::raw('COUNT(*) as total_properties'))
            ->groupBy('section_code')
            ->get();
        // dd($propertiesBySection);

        $sections = [
            'LS1' => [933, 689, 657, 244],
            'LS2A' => [1192, 752, 718, 440],
            'LS2B' => [245, 245, 244, 0],
            'LS3' => [655, 533, 505, 122],
            'LS4' => [1186, 1063, 455, 123],
            'LS5' => [1164, 1088, 994, 76],
            'PS1' => [21360, 18267, 11568, 3093],
            'PS2' => [17920, 15022, 10801, 2898],
            'PS3' => [16323, 13955, 7883, 2368],
            'RPC' => [971, 176, 58, 795]
        ];
        $data = [];

        // Initialize completedProperties outside of the loop
        $completedProperties = 0;

        foreach ($sections as $key => $section) {
            // Reset completedProperties for each section
            $completedProperties = 0;

            foreach ($propertiesBySection as $properties) {
                if ($properties->section_code == $key) {
                    $completedProperties = $properties->total_properties;
                    // Break once the section is found to improve performance
                    break;
                }
            }

            $singleSectionData = [
                'section' => $key,
                'total_properties' => $section[0],
                'history_entered' => $section[1],
                'fully_completed' => $section[2],
                'history_pending' => $section[3],
                'completed_properties' => $completedProperties
            ];
            $data[] = $singleSectionData;
        }


        //dd($totalRehabilitationProperties,$totalNazulProperties);
        $data = [$totalNazulProperties, $totalRehabilitationProperties, $totalId, $totalFreeHoldLeaseHold, $freeHoldProperties, $leaseHoldProperties, $data];
        return $data;
    }

    public function latestRecords()
    {
        $singleProperty = PropertyMaster::latest()->take(100)->get();
    }

    public function filterResults($filter = [], $withLimit =  true)
    {
        // Get sections user is allowed to see
        [$limitToAssigned, $userSectionIds] = getUserAssignedSections();
        $allowedSections = getRequiredSections(); // returns a Collection<Section>

        if ($limitToAssigned) {
            $allowedSections = $allowedSections->whereIn('id', $userSectionIds);
        }

        // Normalize to clean, comparable codes like "PS2"
        $allowedCodes = $allowedSections->pluck('section_code')
            ->map(fn($c) => strtoupper(trim($c)))
            ->values()
            ->all();

        if ($withLimit) {
            $limit  = isset($filter['limit']) ? $filter['limit'] : 50;
            $pageNum = isset($filter['page']) ? $filter['page'] : 1;
            $offset = ($pageNum - 1) * $limit;
        }

        /** For section users the user should be able to get report of properties in their section only */
        $userSectionsIdList = $filter['section_id'] ?? [];
        unset($filter['section_id']);

        /** lesse name filter – this is applied later on cld.lessees_name */
        if (isset($filter['name'])) {
            $where[] = "lesse_name like '%" . $filter['name'] . "%'";
        }
        /** contact filter */
        if (isset($filter['contact'])) {
            $where[] = "phone_no like '" . $filter['contact'] . "%'";
        }

        /** property inspection details – outstandingDues */
        if (isset($filter['outstandingDues'])) {
            $min = $filter['outstandingDues']['min'];
            $max = $filter['outstandingDues']['max'];
            if ($min != "" && $max != "") {
                $totalDues = "total_dues between $min and $max";
            } else {
                if ($min != "") {
                    $totalDues = "total_dues >= $min ";
                }
                if ($max != "") {
                    $totalDues = "total_dues <= $max ";
                }
            }
            $where[] = $totalDues;
        }

        $query = DB::table('property_masters as pm')
            // plain leftJoin without filter condition
            ->leftJoin('splited_property_details as spd', 'pm.id', '=', 'spd.property_master_id')
            ->leftJoin(DB::raw('(select *,case when date_of_expiration is null then null else timestampdiff(year, doe, date_of_expiration ) end as lease_tenure from property_lease_details) as pld'), 'pm.id', '=', 'pld.property_master_id')
            ->leftJoin('old_colonies as oc', 'pm.new_colony_name', '=', 'oc.id')
            ->{isset($filter['reEnteredSince']) ? 'join' : 'leftJoin'}('property_misc_details as pmd', function ($join) {
                $join->on('pm.id', '=', 'pmd.property_master_id')
                    ->where(function ($query) {
                        $query->where(function ($q) {
                            $q->whereNull('pm.is_joint_property')
                                ->whereNull('pmd.splited_property_detail_id');
                        })->orWhere(function ($q) {
                            $q->whereNotNull('pm.is_joint_property')
                                ->whereColumn('pmd.splited_property_detail_id', '=', 'spd.id');
                        });
                    });
            })
            // current_lessee_details join – conditional to fetch the correct lessee for main or split
            ->leftJoin(DB::raw('(select * from current_lessee_details where flat_id is null) as cld'), function ($join) {
                $join->on('pm.id', '=', 'cld.property_master_id')
                    ->where(function ($query) {
                        // Main property: no split detail id
                        $query->where(function ($q) {
                            $q->whereNull('pm.is_joint_property')
                                ->whereNull('cld.splited_property_detail_id');
                        })
                            // Split property: match the split detail id
                            ->orWhere(function ($q) {
                                $q->whereNotNull('pm.is_joint_property')
                                    ->whereColumn('spd.id', '=', 'cld.splited_property_detail_id');
                            });
                    });
            })
            ->leftJoin('property_inspection_demand_details as pid', function ($join) {
                $join->on('pm.id', '=', 'pid.property_master_id')
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereNull('pm.is_joint_property')
                                ->whereNull('pid.splited_property_detail_id');
                        })->orWhere(function ($query) {
                            $query->whereNotNull('pm.is_joint_property')
                                ->whereColumn('spd.id', '=', 'pid.splited_property_detail_id');
                        });
                    });
            })
            ->leftJoin('property_contact_details as pcd', function ($join) {
                $join->on('pm.id', '=', 'pcd.property_master_id')
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereNull('pm.is_joint_property')
                                ->whereNull('pcd.splited_property_detail_id');
                        })->orWhere(function ($query) {
                            $query->whereNotNull('pm.is_joint_property')
                                ->whereColumn('spd.id', '=', 'pcd.splited_property_detail_id');
                        });
                    });
            })
            ->select(
                'pm.id',
                // old_propert_id – use spd.old_property_id when available
                DB::raw("CASE 
                WHEN spd.id IS NOT NULL THEN spd.old_property_id 
                ELSE pm.old_propert_id 
            END AS old_propert_id"),
                // unique_propert_id – use spd.child_prop_id when available (for split properties)
                DB::raw("CASE 
                WHEN spd.id IS NOT NULL THEN spd.child_prop_id 
                ELSE pm.unique_propert_id 
            END AS unique_propert_id"),
                'pm.land_type',
                'pm.new_colony_name',
                'pm.property_type',
                'pm.property_sub_type',
                // property_status – use spd.property_status when available
                DB::raw("CASE 
                WHEN spd.id IS NOT NULL THEN spd.property_status 
                ELSE (CASE WHEN pm.is_joint_property IS NULL THEN pm.status ELSE spd.property_status END) 
            END AS property_status"),
                // area_in_sqm – use spd.area_in_sqm when available
                DB::raw("CASE 
                WHEN spd.id IS NOT NULL THEN spd.area_in_sqm 
                ELSE (CASE WHEN pm.is_joint_property IS NULL THEN pld.plot_area_in_sqm ELSE spd.area_in_sqm END) 
            END AS area_in_sqm"),
                DB::raw("CASE WHEN pmd.is_gr_revised_ever = 1 THEN 'yes' ELSE 'No' END AS gr"),
                // lesse_name – the join already returns the correct one for main or split
                'cld.lessees_name as lesse_name',
                DB::raw('case when date_of_expiration is null then null else timestampdiff(year, doe, date_of_expiration ) end as lease_tenure'),
                'pld.doe',
                'pld.doa',
                'pld.type_of_lease',
                // land_value – use spd.plot_value_cr when available
                DB::raw("CASE 
                WHEN spd.id IS NOT NULL THEN spd.plot_value_cr 
                ELSE (CASE WHEN pm.is_joint_property IS NULL THEN pld.plot_value_cr ELSE spd.plot_value_cr END) 
            END AS land_value"),
                // address – use spd.presently_known_as when available
                DB::raw("CASE 
                WHEN spd.id IS NOT NULL THEN spd.presently_known_as 
                ELSE (CASE WHEN pm.is_joint_property IS NULL THEN pld.presently_known_as ELSE spd.presently_known_as END) 
            END AS address"),
                DB::raw('coalesce(pid.total_dues,0) as total_dues'),
                'pmd.re_rented_date',
                'pcd.phone_no',
                // gr_in_re_rs – use spd.present_ground_rent when available
                DB::raw("CASE 
                WHEN spd.id IS NOT NULL THEN spd.present_ground_rent 
                ELSE (CASE 
                    WHEN pm.is_joint_property IS NULL THEN COALESCE(pld.present_ground_rent, CAST(pld.gr_in_re_rs AS DECIMAL(12,2))) 
                    ELSE spd.present_ground_rent 
                END) 
            END AS gr_in_re_rs")
            )
            // ADD THIS WHEREIN CLAUSE FOR ALLOWED CODES
            ->when(!empty($allowedCodes), function ($q) use ($allowedCodes) {
                return $q->whereIn('pm.section_code', $allowedCodes);
            })
            ->when(isset($filter['land_type']), fn($q) => $q->where('pm.land_type', $filter['land_type']))
            ->when(
                !empty($filter['property_type']),
                fn($q) => $q->whereIn('pm.property_type', $filter['property_type'])
            )
            ->when(
                !empty($filter['property_sub_type']),
                fn($q) => $q->whereIn('pm.property_sub_type', $filter['property_sub_type'])
            )
            ->when(
                !empty($filter['land_status']),
                function ($q) use ($filter) {
                    $case = DB::raw("CASE WHEN pm.is_joint_property IS NULL THEN pm.status ELSE spd.property_status END");
                    return $q->whereIn($case, $filter['land_status']);
                }
            )
            ->when(
                !empty($filter['colony']),
                fn($q) => $q->whereIn('new_colony_name', $filter['colony'])
            )
            // propertyId filter – searches both main and split IDs
            ->when(!empty($filter['propertyId']), function ($q) use ($filter) {
                $q->where(function ($sub) use ($filter) {
                    $sub->where('pm.old_propert_id', 'like', $filter['propertyId'] . '%')
                        ->orWhere('spd.old_property_id', 'like', $filter['propertyId'] . '%');
                });
            })
            ->when(!empty($filter['land_size']), function ($q) use ($filter) {
                $min = (float) $filter['land_size']['min'];
                $max = (float) $filter['land_size']['max'];

                $case = DB::raw("CAST(
                CASE
                    WHEN pm.is_joint_property IS NULL THEN pld.plot_area_in_sqm
                    ELSE spd.area_in_sqm
                END AS DECIMAL(10,2))");
                if ($min > 0 && $max > 0) {
                    $q->whereBetween($case, [$min, $max]);
                } elseif ($min > 0) {
                    $q->where($case, '>=', $min);
                } elseif ($max > 0) {
                    $q->where($case, '<=', $max);
                }
            })
            ->when(!empty($filter['date_of_execution']), function ($q) use ($filter) {
                $min = $filter['date_of_execution']['min'] ?? "";
                $max = $filter['date_of_execution']['max'] ?? "";
                if ($min !== "" && $max !== "") {
                    $q->whereBetween('doe', [$min, $max]);
                } elseif ($min !== "") {
                    $q->where('doe', '>=', $min);
                } elseif ($max !== "") {
                    $q->where('doe', '<=', $max);
                }
            })
            ->when(!empty($filter['date_of_allottment']), function ($q) use ($filter) {
                $min = $filter['date_of_allottment']['min'] ?? "";
                $max = $filter['date_of_allottment']['max'] ?? "";
                if ($min !== "" && $max !== "") {
                    $q->whereBetween('doa', [$min, $max]);
                } elseif ($min !== "") {
                    $q->where('doa', '>=', $min);
                } elseif ($max !== "") {
                    $q->where('doa', '<=', $max);
                }
            })
            ->when(!empty($filter['land_value']), function ($q) use ($filter) {
                $min = (float) $filter['land_value']['min'];
                $max = (float) $filter['land_value']['max'];

                $case = DB::raw("CAST(
                CASE
                    WHEN pm.is_joint_property IS NULL THEN pld.plot_value_cr
                    ELSE spd.plot_value_cr
                END AS DECIMAL(10,2))");
                if ($min > 0 && $max > 0) {
                    $q->whereBetween($case, [$min, $max]);
                } elseif ($min > 0) {
                    $q->where($case, '>=', $min);
                } elseif ($max > 0) {
                    $q->where($case, '<=', $max);
                }
            })
            ->when(
                !empty($filter['leaseDeed']),
                fn($q) => $q->whereIn('type_of_lease', $filter['leaseDeed'])
            )
            ->when(
                !empty($filter['propertyAddress']),
                fn($q) => $q->where('address', 'like', '%' . $filter['propertyAddress'] . '%')
            )
            ->when(!empty($filter['groundRent']), function ($q) use ($filter) {
                $min = $filter['groundRent']['min'] ?? "";
                $max = $filter['groundRent']['max'] ?? "";
                if ($min !== "" && $max !== "") {
                    $q->whereBetween('gr_in_re_rs', [$min, $max]);
                } elseif ($min !== "") {
                    $q->where('gr_in_re_rs', '>=', $min);
                } elseif ($max !== "") {
                    $q->where('gr_in_re_rs', '<=', $max);
                }
            })
            ->when(
                !empty($filter['name']),
                fn($q) => $q->where('cld.lessees_name', 'like', '%' . $filter['name'] . '%')
            )
            ->when(
                !empty($filter['contact']),
                fn($q) => $q->where('phone_no', 'like', $filter['contact'] . '%')
            )
            ->when(!empty($filter['outstandingDues']), function ($q) use ($filter) {
                $min = (float) $filter['outstandingDues']['min'];
                $max = (float) $filter['outstandingDues']['max'];

                $condition = DB::raw("CAST(pid.total_dues AS DECIMAL(10,2))");
                if ($min > 0 && $max > 0) {
                    $q->whereBetween($condition, [$min, $max]);
                } elseif ($min > 0) {
                    $q->where($condition, '>=', $min);
                } elseif ($max > 0) {
                    $q->where($condition, '<=', $max);
                }
            })
            ->when(isset($filter['leaseTenure']), function ($query) use ($filter) {
                $query->where(function ($q) use ($filter) {
                    foreach ($filter['leaseTenure'] as $range) {
                        switch ($range) {
                            case 'Perpetual':
                                $q->orWhereNull('pld.lease_tenure');
                                break;
                            case '0 - 5':
                                $q->orWhereBetween('pld.lease_tenure', [0, 5]);
                                break;
                            case '5 - 25':
                                $q->orWhereBetween('pld.lease_tenure', [5, 25]);
                                break;
                            case '25 - 50':
                                $q->orWhereBetween('pld.lease_tenure', [25, 50]);
                                break;
                            case '50 - 75':
                                $q->orWhereBetween('pld.lease_tenure', [50, 75]);
                                break;
                            case '75+':
                                $q->orWhere('pld.lease_tenure', '>', 75);
                                break;
                        }
                    }
                });
            })
            ->when(isset($filter['reEnteredSince']), function ($query) use ($filter) {
                $query->where(function ($q) use ($filter) {
                    foreach ($filter['reEnteredSince'] as $range) {
                        switch ($range) {
                            case 'y5+':
                                $q->orWhereRaw('DATEDIFF(CURDATE(), pmd.re_rented_date) > ?', [5 * 365]);
                                break;
                            case '1y - 5y':
                                $q->orWhereRaw('DATEDIFF(CURDATE(), pmd.re_rented_date) BETWEEN ? AND ?', [1 * 365, 5 * 365]);
                                break;
                            case '6m - 1y':
                                $q->orWhereRaw('DATEDIFF(CURDATE(), pmd.re_rented_date) BETWEEN ? AND ?', [6 * 30, 12 * 30]);
                                break;
                            case '1m - 6m':
                                $q->orWhereRaw('DATEDIFF(CURDATE(), pmd.re_rented_date) BETWEEN ? AND ?', [1 * 30, 6 * 30]);
                                break;
                            case 'm1-':
                                $q->orWhereRaw('DATEDIFF(CURDATE(), pmd.re_rented_date) < ?', [1 * 30]);
                                break;
                        }
                    }
                });
            });

        // Convert the main query into a subquery and join item tables for display names
        $queryWithNames = DB::table(DB::raw("({$query->toSql()}) as t"))
            ->mergeBindings($query)
            ->leftJoin('items as prop_groups', 't.land_type', '=', 'prop_groups.id')
            ->leftJoin('items as prop_status', 't.property_status', '=', 'prop_status.id')
            ->leftJoin('items as prop_type', 't.property_type', '=', 'prop_type.id')
            ->select(
                't.id',
                't.old_propert_id',
                't.unique_propert_id',
                't.area_in_sqm',
                't.gr',
                't.lesse_name',
                't.lease_tenure',
                't.address',
                't.land_value',
                't.total_dues',
                't.re_rented_date',
                't.phone_no',
                't.gr_in_re_rs',
                'prop_groups.item_name as land_type',
                'prop_status.item_name as status',
                'prop_type.item_name as land_use'
            );

        $counterQuery = clone $query;
        $counter = $counterQuery->count();

        if ($withLimit && is_numeric($limit) && is_numeric($offset)) {
            $finalQuery = $queryWithNames->limit((int)$limit)->offset((int)$offset);
        } else {
            $finalQuery = clone($queryWithNames);
        }
        $rows = $finalQuery->get();

        if (!$withLimit) {
            return $rows;
        } else {
            return ['rows' => $rows, 'counter' => $counter];
        }
    }

    //Commented on 19/08/2026 because splitted property is not searching, so create new function for the same.
    /* public function filterResults($filter = [], $withLimit =  true)
    {
        // Get sections user is allowed to see
        [$limitToAssigned, $userSectionIds] = getUserAssignedSections();
        $allowedSections = getRequiredSections(); // returns a Collection<Section>

        if ($limitToAssigned) {
            $allowedSections = $allowedSections->whereIn('id', $userSectionIds);
        }

        // Normalize to clean, comparable codes like "PS2"
        $allowedCodes = $allowedSections->pluck('section_code')
            ->map(fn($c) => strtoupper(trim($c)))
            ->values()
            ->all();

        if ($withLimit) {
            $limit  = isset($filter['limit']) ? $filter['limit'] : 50;
            $pageNum = isset($filter['page']) ? $filter['page'] : 1;
            $offset = ($pageNum - 1) * $limit;
            //$countRows = "SQL_CALC_FOUND_ROWS";
        }

        
        // $userSectionsIdList = Auth::check() ? Auth::user()->sections->pluck('id')->toArray() : [];
        $userSectionsIdList = $filter['section_id'];
        unset($filter['section_id']);

        
        if (isset($filter['name'])) {
            $where[] = "lesse_name like '%" . $filter['name'] . "%'";
        }
        
        if (isset($filter['contact'])) {
            $where[] = "phone_no like '" . $filter['contact'] . "%'";
        }

        
        if (isset($filter['outstandingDues'])) {
            $min = $filter['outstandingDues']['min'];
            $max = $filter['outstandingDues']['max'];
            if ($min != "" && $max != "") {
                $totalDues = "total_dues between $min and $max";
            } else {
                if ($min != "") {
                    $totalDues = "total_dues >= $min ";
                }
                if ($max != "") {
                    $totalDues =  "total_dues <= $max ";
                }
            }
            $where[] = $totalDues;
        }

        $query = DB::table('property_masters as pm')
            ->leftJoin('splited_property_details as spd', 'pm.id', '=', 'spd.property_master_id')
            ->leftJoin(DB::raw('(select *,case when date_of_expiration is null then null else timestampdiff(year, doe, date_of_expiration ) end as lease_tenure from property_lease_details) as pld'), 'pm.id', '=', 'pld.property_master_id')
            ->leftJoin('old_colonies as oc', 'pm.new_colony_name', '=', 'oc.id')
            ->{isset($filter['reEnteredSince']) ? 'join' : 'leftJoin'}('property_misc_details as pmd', function ($join) {
                $join->on('pm.id', '=', 'pmd.property_master_id')
                    ->where(function ($query) {
                        $query->where(function ($q) {
                            $q->whereNull('pm.is_joint_property')
                                ->whereNull('pmd.splited_property_detail_id');
                        })->orWhere(function ($q) {
                            $q->whereNotNull('pm.is_joint_property')
                                ->whereColumn('pmd.splited_property_detail_id', '=', 'spd.id');
                        });
                    });
            })
            
            ->leftJoin(DB::raw('(select * from current_lessee_details where flat_id is null) as cld'), function ($join) {
                $join->on('pm.id', '=', 'cld.property_master_id')
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereNull('pm.is_joint_property')
                                ->whereNull('cld.splited_property_detail_id');
                        })->orWhere(function ($query) {
                            $query->whereNotNull('pm.is_joint_property')
                                ->whereColumn('spd.id', '=', 'cld.splited_property_detail_id');
                        });
                    });
            })
            ->leftJoin('property_inspection_demand_details as pid', function ($join) {
                $join->on('pm.id', '=', 'pid.property_master_id')
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereNull('pm.is_joint_property')
                                ->whereNull('pid.splited_property_detail_id');
                        })->orWhere(function ($query) {
                            $query->whereNotNull('pm.is_joint_property')
                                ->whereColumn('spd.id', '=', 'pid.splited_property_detail_id');
                        });
                    });
            })
            ->leftJoin('property_contact_details as pcd', function ($join) {
                $join->on('pm.id', '=', 'pcd.property_master_id')
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereNull('pm.is_joint_property')
                                ->whereNull('pcd.splited_property_detail_id');
                        })->orWhere(function ($query) {
                            $query->whereNotNull('pm.is_joint_property')
                                ->whereColumn('spd.id', '=', 'pcd.splited_property_detail_id');
                        });
                    });
            })
            ->select(
                'pm.id',
                'pm.old_propert_id',
                'pm.unique_propert_id',
                'pm.land_type',
                'pm.new_colony_name',
                'pm.property_type',
                'pm.property_sub_type',
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pm.status ELSE spd.property_status END AS property_status'),
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pld.plot_area_in_sqm ELSE spd.area_in_sqm END AS area_in_sqm'),
                DB::raw("CASE WHEN pmd.is_gr_revised_ever = 1 THEN 'yes' ELSE 'No' END AS gr"),
                'cld.lessees_name as lesse_name',
                DB::raw('case when date_of_expiration is null then null else timestampdiff(year, doe, date_of_expiration ) end as lease_tenure'),
                'pld.doe',
                'pld.doa',
                'pld.type_of_lease',
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pld.plot_value_cr ELSE spd.plot_value_cr END AS land_value'),
                DB::raw('case when pm.is_joint_property is null then pld.presently_known_as else spd.presently_known_as end as address'),
                DB::raw('coalesce(pid.total_dues,0) as total_dues'),
                'pmd.re_rented_date',
                'pcd.phone_no',
                DB::raw('CASE 
                    WHEN pm.is_joint_property IS NULL THEN COALESCE(pld.present_ground_rent, CAST(pld.gr_in_re_rs AS DECIMAL(12,2))) 
                    ELSE spd.present_ground_rent 
                    END AS gr_in_re_rs')
            )
            // ADD THIS WHEREIN CLAUSE FOR ALLOWED CODES
            ->when(!empty($allowedCodes), function ($q) use ($allowedCodes) {
                return $q->whereIn('pm.section_code', $allowedCodes);
            })
            
            ->when(isset($filter['land_type']), fn($q) => $q->where('pm.land_type', $filter['land_type']))

            ->when(
                !empty($filter['property_type']),
                fn($q) =>
                $q->whereIn('pm.property_type', $filter['property_type'])
            )

            ->when(
                !empty($filter['property_sub_type']),
                fn($q) =>
                $q->whereIn('pm.property_sub_type', $filter['property_sub_type'])
            )

            ->when(
                !empty($filter['land_status']),
                function ($q) use ($filter) {
                    $case = DB::raw("CASE WHEN pm.is_joint_property IS NULL THEN pm.status ELSE spd.property_status END");
                    return $q->whereIn($case, $filter['land_status']);
                }
            )

            ->when(
                !empty($filter['colony']),
                fn($q) =>
                $q->whereIn('new_colony_name', $filter['colony'])
            )

            ->when(
                !empty($filter['propertyId']),
                fn($q) =>
                $q->where('old_propert_id', 'like', $filter['propertyId'] . '%')
            )

            ->when(!empty($filter['land_size']), function ($q) use ($filter) {
                $min = (float) $filter['land_size']['min'];
                $max = (float) $filter['land_size']['max'];

                $case = DB::raw("CAST(
                    CASE
                        WHEN pm.is_joint_property IS NULL THEN pld.plot_area_in_sqm
                        ELSE spd.area_in_sqm
                    END AS DECIMAL(10,2))");
                if ($min > 0 && $max > 0) {
                    $q->whereBetween($case, [$min, $max]);
                } elseif ($min > 0) {
                    $q->where($case, '>=', $min);
                } elseif ($max > 0) {
                    $q->where($case, '<=', $max);
                }
            })

            ->when(!empty($filter['date_of_execution']), function ($q) use ($filter) {
                $min = $filter['date_of_execution']['min'] ?? "";
                $max = $filter['date_of_execution']['max'] ?? "";
                if ($min !== "" && $max !== "") {
                    $q->whereBetween('doe', [$min, $max]);
                } elseif ($min !== "") {
                    $q->where('doe', '>=', $min);
                } elseif ($max !== "") {
                    $q->where('doe', '<=', $max);
                }
            })
            ->when(!empty($filter['date_of_allottment']), function ($q) use ($filter) {
                $min = $filter['date_of_allottment']['min']??"";
                $max = $filter['date_of_allottment']['max']??"";

                if ($min !== "" && $max !== "") {
                    $q->whereBetween('doa', [$min, $max]);
                } elseif ($min !== "") {
                    $q->where('doa', '>=', $min);
                } elseif ($max !== "") {
                    $q->where('doa', '<=', $max);
                }
            })
            ->when(!empty($filter['land_value']), function ($q) use ($filter) {
                $min = (float) $filter['land_value']['min'];
                $max = (float) $filter['land_value']['max'];

                $case = DB::raw("CAST(
                    CASE
                        WHEN pm.is_joint_property IS NULL THEN pld.plot_value_cr
                        ELSE spd.plot_value_cr
                    END AS DECIMAL(10,2))");
                if ($min > 0 && $max > 0) {
                    $q->whereBetween($case, [$min, $max]);
                } elseif ($min > 0) {
                    $q->where($case, '>=', $min);
                } elseif ($max > 0) {
                    $q->where($case, '<=', $max);
                }
            })

            ->when(
                !empty($filter['leaseDeed']),
                fn($q) =>
                $q->whereIn('type_of_lease', $filter['leaseDeed'])
            )

            ->when(
                !empty($filter['propertyAddress']),
                fn($q) =>
                $q->where('address', 'like', '%' . $filter['propertyAddress'] . '%')
            )

            ->when(!empty($filter['groundRent']), function ($q) use ($filter) {
                $min = $filter['groundRent']['min'] ?? "";
                $max = $filter['groundRent']['max'] ?? "";

                if ($min !== "" && $max !== "") {
                    $q->whereBetween('gr_in_re_rs', [$min, $max]);
                } elseif ($min !== "") {
                    $q->where('gr_in_re_rs', '>=', $min);
                } elseif ($max !== "") {
                    $q->where('gr_in_re_rs', '<=', $max);
                }
            })
            ->when(
                !empty($filter['name']),
                fn($q) =>
                $q->where('cld.lessees_name', 'like', '%' . $filter['name'] . '%')
            )

            ->when(
                !empty($filter['contact']),
                fn($q) =>
                $q->where('phone_no', 'like', $filter['contact'] . '%')
            )

            ->when(!empty($filter['outstandingDues']), function ($q) use ($filter) {
                $min = (float) $filter['outstandingDues']['min'];
                $max = (float) $filter['outstandingDues']['max'];

                $condition = DB::raw("CAST(pid.total_dues AS DECIMAL(10,2))");
                if ($min > 0 && $max > 0) {
                    $q->whereBetween($condition, [$min, $max]);
                } elseif ($min > 0) {
                    $q->where($condition, '>=', $min);
                } elseif ($max > 0) {
                    $q->where($condition, '<=', $max);
                }
            })
            ->when(isset($filter['leaseTenure']), function ($query) use ($filter) {
                $query->where(function ($q) use ($filter) {
                    foreach ($filter['leaseTenure'] as $range) {
                        switch ($range) {
                            case 'Perpetual':
                                $q->orWhereNull('pld.lease_tenure');
                                break;
                            case '0 - 5':
                                $q->orWhereBetween('pld.lease_tenure', [0, 5]);
                                break;
                            case '5 - 25':
                                $q->orWhereBetween('pld.lease_tenure', [5, 25]);
                                break;
                            case '25 - 50':
                                $q->orWhereBetween('pld.lease_tenure', [25, 50]);
                                break;
                            case '50 - 75':
                                $q->orWhereBetween('pld.lease_tenure', [50, 75]);
                                break;
                            case '75+':
                                $q->orWhere('pld.lease_tenure', '>', 75);
                                break;
                        }
                    }
                });
            })
            ->when(isset($filter['reEnteredSince']), function ($query) use ($filter) {
                $query->where(function ($q) use ($filter) {
                    foreach ($filter['reEnteredSince'] as $range) {
                        switch ($range) {
                            case 'y5+':
                                $q->orWhereRaw('DATEDIFF(CURDATE(), pmd.re_rented_date) > ?', [5 * 365]);
                                break;
                            case '1y - 5y':
                                $q->orWhereRaw('DATEDIFF(CURDATE(), pmd.re_rented_date) BETWEEN ? AND ?', [1 * 365, 5 * 365]);
                                break;
                            case '6m - 1y':
                                $q->orWhereRaw('DATEDIFF(CURDATE(), pmd.re_rented_date) BETWEEN ? AND ?', [6 * 30, 12 * 30]);
                                break;
                            case '1m - 6m':
                                $q->orWhereRaw('DATEDIFF(CURDATE(), pmd.re_rented_date) BETWEEN ? AND ?', [1 * 30, 6 * 30]);
                                break;
                            case 'm1-':
                                $q->orWhereRaw('DATEDIFF(CURDATE(), pmd.re_rented_date) < ?', [1 * 30]);
                                break;
                        }
                    }
                });
            });
        // dd($query->toSql(), $query->getBindings());
        $queryWithNames = DB::table(DB::raw("({$query->toSql()}) as t"))
            ->mergeBindings($query) // Required to merge bindings from the subquery
            ->leftJoin('items as prop_groups', 't.land_type', '=', 'prop_groups.id')
            ->leftJoin('items as prop_status', 't.property_status', '=', 'prop_status.id')
            ->leftJoin('items as prop_type', 't.property_type', '=', 'prop_type.id')
            ->select(
                't.id',
                't.old_propert_id',
                't.unique_propert_id',
                't.area_in_sqm',
                't.gr',
                't.lesse_name',
                't.lease_tenure',
                't.address',
                't.land_value',
                't.total_dues',
                't.re_rented_date',
                't.phone_no',
                't.gr_in_re_rs',
                'prop_groups.item_name as land_type',
                'prop_status.item_name as status',
                'prop_type.item_name as land_use'
            );
        $counterQuery = clone $query;
        $counter = $counterQuery->count();
        
        if ($withLimit && is_numeric($limit) && is_numeric($offset)) {
            $finalQuery = $queryWithNames->limit((int)$limit)->offset((int)$offset);
        } else {
            $finalQuery = clone ($queryWithNames);
        }
        $rows = $finalQuery->get();
        if (!$withLimit) {
            return $rows;
        } else {
            return ['rows' => $rows, 'counter' => $counter];
        }
    } */


    public function getDistinctSubTypes($types = [])
    {
        if ($types == null) {
            $types = [];
        }
        if (count($types) > 0) {
            $qMark = implode(', ', array_fill(0, count($types), '?'));
            $query = "SELECT 
                        items.id, item_name
                    FROM
                        items
                            JOIN
                        (SELECT DISTINCT
                            (sub_type)
                        FROM
                            property_type_sub_type_mapping
                        WHERE
                            type IN ($qMark)) t ON items.id = t.sub_type
                    ORDER BY item_name;";
            // exit($query);
            $subtypes = DB::select($query, $types);
            return $subtypes;
        } else {
            return [];
        }
    }
    public function detailedReport($filter = [], $export = false, $perpage = null, $page = null)
    {
        $user = Auth::user();

        // Determine if we need to filter by user sections
        $filterUserSections = isset($filter['section_id']) || $user->hasAnyRole('section-officer', 'deputy-lndo');
        $userSectionIdList = isset($filter['section_id'])
            ? $filter['section_id']
            : ($user->sections->pluck('section_code')->toArray() ?? []);



        $sectionCodes = DB::table('sections')
            ->whereIn('id', $userSectionIdList)
            ->pluck('section_code')
            ->toArray();

        // dd($sectionCodes);


        // Start building the query
        $query = DB::table('property_masters as pm')
            ->select(
                'pm.id',
                'pm.unique_propert_id',
                'pm.old_propert_id',
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN NULL ELSE spd.child_prop_id END AS child_prop_id'),
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN NULL ELSE spd.old_property_id END AS child_0ld_prop_id'),
                'pm.property_type',
                'pm.property_sub_type',
                'pm.land_type',
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pm.status ELSE spd.property_status END AS property_status'),
                'pm.section_code as section',
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pld.presently_known_as ELSE spd.presently_known_as END AS address'),
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pld.plot_area_in_sqm ELSE spd.area_in_sqm END AS area_in_sqm'),
                'cld.lessees_name as current_lesse_name',
                'pld.doe',
                'ptld.transfer_date as transferDate',

            )
            ->leftJoin('property_lease_details as pld', 'pm.id', '=', 'pld.property_master_id')
            ->leftJoin('splited_property_details as spd', 'pm.id', '=', 'spd.property_master_id')
            ->leftJoin(DB::raw('(select * from current_lessee_details where flat_id is null) as cld'), function ($join) {
                $join->on('pm.id', '=', 'cld.property_master_id')
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereNull('pm.is_joint_property')
                                ->whereNull('cld.splited_property_detail_id');
                        })->orWhere(function ($query) {
                            $query->whereNotNull('pm.is_joint_property')
                                ->whereColumn('spd.id', '=', 'cld.splited_property_detail_id');
                        });
                    });
            })
            ->leftJoin(DB::raw('(
                SELECT
                    property_master_id,
                    splited_property_detail_id,
                    MAX(transferDate) AS transfer_date
                FROM property_transferred_lessee_details
                WHERE flat_id IS NULL
                AND process_of_transfer = "Conversion"
                GROUP BY
                    property_master_id,
                    splited_property_detail_id
            ) AS ptld'), function ($join) {
                $join->on('pm.id', '=', 'ptld.property_master_id')
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereNull('pm.is_joint_property')
                                ->whereNull('ptld.splited_property_detail_id');
                        })->orWhere(function ($query) {
                            $query->whereNotNull('pm.is_joint_property')
                                ->whereColumn('spd.id', '=', 'ptld.splited_property_detail_id');
                        });
                    });
            });


        // Apply user section filter if needed - BEFORE other filters
        if ($sectionCodes && !empty($userSectionIdList)) {
            $query->whereIn('pm.section_code', $sectionCodes);
        }

        // Join items table for property_type, property_sub_type, and property_status
        $query->leftJoin('items as item_type', 'item_type.id', '=', 'pm.property_type')
            ->leftJoin('items as latest_item_type', 'latest_item_type.id', '=', 'pld.property_type_at_present')
            ->leftJoin('items as original_item_type', 'original_item_type.id', '=', 'pld.property_type_as_per_lease')
            ->leftJoin('items as original_item_subtype', 'original_item_subtype.id', '=', 'pld.property_sub_type_as_per_lease')
            ->leftJoin('items as land_type_names', 'land_type_names.id', '=', 'pm.land_type')
            ->leftJoin('items as item_sub_type', 'item_sub_type.id', '=', 'pm.property_sub_type')
            ->leftJoin('items as latest_item_sub_type', 'latest_item_sub_type.id', '=', 'pld.property_sub_type_at_present')
            ->leftJoin('items as item_property_status', function ($join) {
                $join->on('item_property_status.id', '=', DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pm.status ELSE spd.property_status END'));
            });

        // Select item names
        $query->addSelect(
            'item_type.item_name as propertyType',
            'item_sub_type.item_name as propertySubtype',
            'item_property_status.item_name as propertyStatus',
            'land_type_names.item_name as landType',
            'latest_item_type.item_name as latestPropertyType',
            'latest_item_sub_type.item_name as latestPropertySubType',
        );

        // Add export-specific columns only when needed
        if ($export || ($perpage && $page)) {
            $query->addSelect(
                'pm.unique_file_no',
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pld.plot_area ELSE spd.current_area END AS area'),
                'oc.name as colony',
                'pm.block_no as block',
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pm.plot_or_property_no ELSE spd.plot_flat_no END AS plot_no'),
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pld.presently_known_as ELSE spd.presently_known_as END AS presently_known_as'),
                'pld.doa as date_of_allotment',
                'pld.doe as date_of_execution',
                'pld.date_of_expiration',
                DB::raw("CASE WHEN pld.is_land_use_changed = 1 THEN 'yes' ELSE 'no' END AS is_land_use_changed"),
                DB::raw("CASE WHEN pmd.is_gr_revised_ever = 1 THEN 'yes' ELSE 'no' END AS rgr"),
                'pld.start_date_of_gr',
                'pld.first_rgr_due_on',
                'pld.rgr_duration',
                'pld.property_type_at_present',
                'pld.property_sub_type_at_present',
                'pid.last_inspection_ir_date',
                'pid.last_demand_id',
                'pid.last_demand_letter_date',
                'pid.last_demand_amount',
                'pid.last_amount_received',
                'pid.last_amount_received_date',
                'pid.total_dues',
                'pcd.address as lessee_address',
                'pcd.phone_no as lessee_phone',
                'pcd.email as lessee_email',
                'users.name as created_by',
                'pm.created_at',
                'pld.premium',
                'pld.premium_in_paisa',
                'pld.type_of_lease',
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN COALESCE(pld.present_ground_rent, pld.gr_in_re_rs) ELSE COALESCE(spd.present_ground_rent, pld.gr_in_re_rs) END AS ground_rent')
            )
                ->leftJoin('old_colonies as oc', 'pm.new_colony_name', '=', 'oc.id')
                ->leftJoin('users', 'pm.created_by', '=', 'users.id');

            // Add conditional joins for split properties
            $query->leftJoin('property_inspection_demand_details as pid', function ($join) {
                $join->on('pm.id', '=', 'pid.property_master_id')
                    ->where(function ($q) {
                        $q->where(function ($q2) {
                            $q2->whereNull('pm.is_joint_property')
                                ->whereNull('pid.splited_property_detail_id');
                        })->orWhere(function ($q2) {
                            $q2->whereNotNull('pm.is_joint_property')
                                ->whereColumn('spd.id', '=', 'pid.splited_property_detail_id');
                        });
                    });
            })
                ->leftJoin('property_contact_details as pcd', function ($join) {
                    $join->on('pm.id', '=', 'pcd.property_master_id')
                        ->where(function ($q) {
                            $q->where(function ($q2) {
                                $q2->whereNull('pm.is_joint_property')
                                    ->whereNull('pcd.splited_property_detail_id');
                            })->orWhere(function ($q2) {
                                $q2->whereNotNull('pm.is_joint_property')
                                    ->whereColumn('spd.id', '=', 'pcd.splited_property_detail_id');
                            });
                        });
                })
                ->leftJoin('property_misc_details as pmd', function ($join) {
                    $join->on('pm.id', '=', 'pmd.property_master_id')
                        ->where(function ($q) {
                            $q->where(function ($q2) {
                                $q2->whereNull('pm.is_joint_property')
                                    ->whereNull('pmd.splited_property_detail_id');
                            })->orWhere(function ($q2) {
                                $q2->whereNotNull('pm.is_joint_property')
                                    ->whereColumn('spd.id', '=', 'pmd.splited_property_detail_id');
                            });
                        });
                });

            // Join items for additional export fields
            $query->leftJoin('items as lease_type_names', 'lease_type_names.id', '=', 'pld.type_of_lease')
                ->leftJoin('items as item_present_type_names', 'item_present_type_names.id', '=', 'pld.property_type_at_present')
                ->leftJoin('items as item_present_sub_type_names', 'item_present_sub_type_names.id', '=', 'pld.property_sub_type_at_present');

            $query->addSelect(
                'lease_type_names.item_name as leaseDeed',
                'item_present_type_names.item_name as presentPropertyType',
                'item_present_sub_type_names.item_name as presentPropertySubtype',
                'original_item_type.item_name as originalPropertyType',
                'original_item_subtype.item_name as originalPropertySubType',
            );
        }

        // Apply request filters if provided
        if (!empty($filter)) {
            // Apply each filter only if it exists and has a value
            if (isset($filter['colony']) && !empty($filter['colony'])) {
                $query->whereIn('pm.new_colony_name', $filter['colony']);
            }

            if (isset($filter['landType']) && $filter['landType'] != "") {
                $query->where('pm.land_type', $filter['landType']);
            }

            if (isset($filter['property_status']) && !empty($filter['property_status'])) {
                $query->whereIn('item_property_status.id', $filter['property_status']);
            }

            if (isset($filter['property_type']) && !empty($filter['property_type'])) {
                $query->whereIn('pm.property_type', $filter['property_type']);
            }

            if (isset($filter['property_sub_type']) && !empty($filter['property_sub_type'])) {
                $query->whereIn('pm.property_sub_type', $filter['property_sub_type']);
            }

            if (isset($filter['leaseDeed']) && !empty($filter['leaseDeed'])) {
                $query->whereIn('pld.type_of_lease', $filter['leaseDeed']);
            }

            // Note: section_id filter is already handled at the beginning
            // Only apply if not already handled by user section filter
            if (!empty($sectionCodes)) {
                $query->whereIn('pm.section_code', $sectionCodes);
            }
        }

        // Execute query based on parameters
        if ($export) {
            return $query->get();
        }

        if (!is_null($page) && !is_null($perpage)) {
            return [
                'counter' => (clone $query)->count(),
                'rows' => $query->offset(($page - 1) * $perpage)->limit($perpage)->get(),
            ];
        }

        return $query->paginate(50);
    }
    // public function detailedReport($filter = [], $export = false, $perpage = null, $page = null)
    // {
    //     // Start building the query
    //     $filterUserSections = false;
    //     $userSectionIdList = [];
    //     // if (!$export) {
    //     $user = Auth::user();
    //     //filter by section when section filter is in request or user has role section officer // added by nitin on 27 feb 2025
    //     $filterUserSections = isset($filter['section_id']) ? true : $user->hasAnyRole('section-officer', 'deputy-lndo');
    //     $userSectionIdList = isset($filter['section_id']) ? $filter['section_id'] : $user->sections->pluck('section_code')->toArray();
    //     // }
    //     // dd($filter['section_id'], $filterUserSections, $userSectionIdList);

    //     $query = DB::table('property_masters as pm')
    //         ->select(
    //             'pm.id',
    //             'pm.unique_propert_id',
    //             'pm.old_propert_id',
    //             DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN NULL ELSE spd.child_prop_id END AS child_prop_id'),
    //             'pm.property_type',
    //             'pm.property_sub_type',
    //             'pm.land_type',
    //             DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pm.status ELSE spd.property_status END AS property_status'),
    //             /* 'pm.file_no', */
    //             // 'sections.section_code as section',
    //             'pm.section_code as section',
    //             DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pld.presently_known_as ELSE spd.presently_known_as END AS address'),
    //             DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pld.plot_area_in_sqm ELSE spd.area_in_sqm END AS area_in_sqm'),
    //             'cld.lessees_name as current_lesse_name',
    //             'pld.doe'
    //         )
    //         ->leftJoin('property_lease_details as pld', 'pm.id', '=', 'pld.property_master_id')
    //         ->leftJoin('splited_property_details as spd', 'pm.id', '=', 'spd.property_master_id')
    //         ->leftJoin(DB::raw('(select * from current_lessee_details where flat_id is null) as cld'), function ($join) {
    //             $join->on('pm.id', '=', 'cld.property_master_id')
    //                 ->where(function ($query) {
    //                     $query->where(function ($query) {
    //                         $query->whereNull('pm.is_joint_property')
    //                             ->whereNull('cld.splited_property_detail_id');
    //                     })->orWhere(function ($query) {
    //                         $query->whereNotNull('pm.is_joint_property')
    //                             ->whereColumn('spd.id', '=', 'cld.splited_property_detail_id');
    //                     });
    //                 });
    //         });

    //     // Join items table for property_type, property_sub_type, and property_status
    //     $query->leftJoin('items as item_type', 'item_type.id', '=', 'pm.property_type')
    //         ->leftJoin('items as latest_item_type', 'latest_item_type.id', '=', 'pld.property_type_at_present')
    //         ->leftJoin('items as land_type_names', 'land_type_names.id', '=', 'pm.land_type')
    //         ->leftJoin('items as item_sub_type', 'item_sub_type.id', '=', 'pm.property_sub_type')
    //         ->leftJoin('items as latest_item_sub_type', 'latest_item_sub_type.id', '=', 'pld.property_sub_type_at_present')
    //         ->leftJoin('items as item_property_status', function ($join) {
    //             $join->on('item_property_status.id', '=', DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pm.status ELSE spd.property_status END'));
    //         })
    //         /* ->when($filterUserSections, function ($query) use ($userSectionIdList) {
    //             return $query->join('property_section_mappings as psm', function ($join) use ($userSectionIdList) {
    //                 $join->on('pm.new_colony_name', '=', 'psm.colony_id');
    //                 $join->whereColumn('pm.property_type', 'psm.property_type');
    //                 $join->whereColumn('pm.property_sub_type', 'psm.property_subtype');
    //                 $join->whereIn('psm.section_id', $userSectionIdList);
    //             });
    //         }, function ($query) {
    //             return $query->leftJoin('property_section_mappings as psm', function ($join) {
    //                 $join->on('pm.new_colony_name', '=', 'psm.colony_id');
    //                 $join->whereColumn('pm.property_type', 'psm.property_type');
    //                 $join->whereColumn('pm.property_sub_type', 'psm.property_subtype');
    //             });
    //         })
    //         ->leftJoin('sections', 'psm.section_id', 'sections.id')*/
    //         /* ->when(!empty($userSectionIdList), function($que) use($userSectionIdList){
    //             return $que->whereIn('pm.section_code',$userSectionIdList);
    //         }) */
    //         ;

    //     // Select item names
    //     $query->addSelect(
    //         'item_type.item_name as propertyType',
    //         'item_sub_type.item_name as propertySubtype',
    //         'item_property_status.item_name as propertyStatus',
    //         'land_type_names.item_name as landType',
    //         'latest_item_type.item_name as latestPropertyType',
    //         'latest_item_sub_type.item_name as latestPropertySubType',
    //     );
    //     if ($export || ($perpage && $page)) {
    //         $query->addSelect(
    //             'pm.unique_file_no',
    //             DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pld.plot_area ELSE spd.current_area END AS area'),
    //             'oc.name as colony',
    //             'pm.block_no as block',
    //             DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pm.plot_or_property_no ELSE spd.plot_flat_no END AS plot_no'),
    //             DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pld.presently_known_as ELSE spd.presently_known_as END AS presently_known_as'),
    //             'pld.doa as date_of_allotment',
    //             'pld.doe as date_of_execution',
    //             'pld.date_of_expiration',
    //             DB::raw("CASE WHEN pld.is_land_use_changed = 1 THEN 'yes' ELSE 'no' END AS is_land_use_changed"),
    //             DB::raw("CASE WHEN pmd.is_gr_revised_ever = 1 THEN 'yes' ELSE 'no' END AS rgr"),
    //             'pld.start_date_of_gr',
    //             'pld.first_rgr_due_on',
    //             'pld.rgr_duration',
    //             'pld.property_type_at_present',
    //             'pld.property_sub_type_at_present',
    //             'pid.last_inspection_ir_date',
    //             'pid.last_demand_id',
    //             'pid.last_demand_letter_date',
    //             'pid.last_demand_amount',
    //             'pid.last_amount_received',
    //             'pid.last_amount_received_date',
    //             'pid.total_dues',
    //             'pcd.address as lessee_address',
    //             'pcd.phone_no as lessee_phone',
    //             'pcd.email as lessee_email',
    //             'users.name as created_by',
    //             'pm.created_at',
    //             'pld.premium',
    //             'pld.premium_in_paisa',
    //             'pld.type_of_lease',
    //             DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN COALESCE(pld.present_ground_rent, pld.gr_in_re_rs) ELSE COALESCE(spd.present_ground_rent, pld.gr_in_re_rs) END AS ground_rent')
    //         )
    //             ->leftJoin('old_colonies as oc', 'pm.new_colony_name', '=', 'oc.id')
    //             ->leftJoin('property_inspection_demand_details as pid', function ($join) {
    //                 $join->on('pm.id', '=', 'pid.property_master_id')
    //                     ->where(function ($query) {
    //                         $query->where(function ($query) {
    //                             $query->whereNull('pm.is_joint_property')
    //                                 ->whereNull('pid.splited_property_detail_id');
    //                         })->orWhere(function ($query) {
    //                             $query->whereNotNull('pm.is_joint_property')
    //                                 ->whereColumn('spd.id', '=', 'pid.splited_property_detail_id');
    //                         });
    //                     });
    //             })
    //             ->leftJoin('property_contact_details as pcd', function ($join) {
    //                 $join->on('pm.id', '=', 'pcd.property_master_id')
    //                     ->where(function ($query) {
    //                         $query->where(function ($query) {
    //                             $query->whereNull('pm.is_joint_property')
    //                                 ->whereNull('pcd.splited_property_detail_id');
    //                         })->orWhere(function ($query) {
    //                             $query->whereNotNull('pm.is_joint_property')
    //                                 ->whereColumn('spd.id', '=', 'pcd.splited_property_detail_id');
    //                         });
    //                     });
    //             })
    //             ->leftJoin('property_misc_details as pmd', function ($join) {
    //                 $join->on('pm.id', '=', 'pmd.property_master_id')
    //                     ->where(function ($query) {
    //                         $query->where(function ($query) {
    //                             $query->whereNull('pm.is_joint_property')
    //                                 ->whereNull('pmd.splited_property_detail_id');
    //                         })->orWhere(function ($query) {
    //                             $query->whereNotNull('pm.is_joint_property')
    //                                 ->whereColumn('spd.id', '=', 'pmd.splited_property_detail_id');
    //                         });
    //                     });
    //             })
    //             /* ->leftJoin('current_lessee_details as cld', function ($join) {
    //                 $join->on('pm.id', '=', 'cld.property_master_id')
    //                     ->where(function ($query) {
    //                         $query->where(function ($query) {
    //                             $query->whereNull('pm.is_joint_property')
    //                                 ->whereNull('cld.splited_property_detail_id');
    //                         })->orWhere(function ($query) {
    //                             $query->whereNotNull('pm.is_joint_property')
    //                                 ->whereColumn('spd.id', '=', 'cld.splited_property_detail_id');
    //                         });
    //                     });
    //             }) */
    //             ->leftJoin('users', 'pm.created_by', '=', 'users.id');

    //         // Join items for land_type, lease_type, present property type and subtype
    //         $query->leftJoin('items as lease_type_names', 'lease_type_names.id', '=', 'pld.type_of_lease')
    //             ->leftJoin('items as item_present_type_names', 'item_present_type_names.id', '=', 'pld.property_type_at_present')
    //             ->leftJoin('items as item_present_sub_type_names', 'item_present_sub_type_names.id', '=', 'pld.property_sub_type_at_present');

    //         // Add item names to select
    //         $query->addSelect(
    //             'lease_type_names.item_name as leaseDeed',
    //             'item_present_type_names.item_name as presentPropertyType',
    //             'item_present_sub_type_names.item_name as presentPropertySubtype'
    //         );
    //     }


    //     // Apply filters
    //     if (!empty($filter)) {
    //         if (isset($filter['colony'])) {
    //             $query->whereIn('pm.new_colony_name', $filter['colony']);
    //         }
    //         if (isset($filter['landType']) && $filter['landType'] != "") {
    //             $query->where('pm.land_type', $filter['landType']);
    //         }
    //         if (isset($filter['property_status'])) {
    //             $query->whereIn('item_property_status.id', $filter['property_status']);
    //         }
    //         if (isset($filter['property_type'])) {
    //             $query->whereIn('pm.property_type', $filter['property_type']);
    //         }
    //         if (isset($filter['property_sub_type'])) {
    //             $query->whereIn('pm.property_sub_type', $filter['property_sub_type']);
    //         }
    //         if (isset($filter['leaseDeed'])) {
    //             $query->whereIn('pld.type_of_lease', $filter['leaseDeed']);
    //         }
    //     }

    //     // For debugging, you can uncomment the line below to see the generated SQL
    //     // Log::info($query->toSql() . ' and bindings' . json_encode($query->getBindings()));
    //     // dd($query->toSql(), $query->getBindings());
    //     // dd($query->count());
    //     if ($export) {
    //         return $query->get();
    //     }
    //     if (!is_null($page) && !is_null($perpage)) {
    //         return [
    //             'counter' => (clone $query)->count(),
    //             'rows' => $query->offset(($page - 1) * $perpage)->limit($perpage)->get(),
    //         ];
    //     }
    //     return $query->paginate(50);
    // }

    public function sectionwisePropertyCount($export, $page = 1)
    {
        list($filterUserSections, $userSectionIdList) = getUserAssignedSections();
        $query = DB::table('property_masters', 'pm')
            ->leftJoin('splited_property_details as spd', 'pm.id', '=', 'spd.property_master_id')
            ->when($filterUserSections, function ($query) use ($userSectionIdList) {
                return $query->join('property_section_mappings as psm', function ($join) use ($userSectionIdList) {
                    $join->on('pm.new_colony_name', '=', 'psm.colony_id');
                    $join->whereColumn('pm.property_type', 'psm.property_type');
                    $join->whereColumn('pm.property_sub_type', 'psm.property_subtype');
                    $join->whereIn('psm.section_id', $userSectionIdList);
                });
            }, function ($query) {
                return $query->leftJoin('property_section_mappings as psm', function ($join) {
                    $join->on('pm.new_colony_name', '=', 'psm.colony_id');
                    $join->whereColumn('pm.property_type', 'psm.property_type');
                    $join->whereColumn('pm.property_sub_type', 'psm.property_subtype');
                });
            })
            ->join('sections', 'psm.section_id', '=', 'sections.id')
            ->select('sections.id', 'sections.name as section_name', DB::raw('count(pm.id) as number_of_properties'))
            ->groupBy('sections.id', 'sections.name');
        if ($export) {
            return $query->get();
        } else {
            $paginated = $query->paginate(50, ['*'], 'page', $page);

            return [
                'headerValues' => ['section_name', 'number_of_properties'],
                'dataValues' => $paginated->items(),
                'pagination' => [
                    'total' => $paginated->total(),
                    'per_page' => $paginated->perPage(),
                    'current_page' => $paginated->currentPage(),
                    'last_page' => $paginated->lastPage(),
                ]
            ];
        }
    }

    //Comment on dated 13/11/2025 (Lalit)
    /* public function sectionwisePropertyCountNew($export, $page = 1)
    {
        list($filterUserSections, $userSectionIdList) = getUserAssignedSections();
        $query = DB::table('property_masters', 'pm')
            ->leftJoin('splited_property_details as spd', 'pm.id', '=', 'spd.property_master_id')
            ->when($filterUserSections, function ($query) use ($userSectionIdList) {
                return $query->join('property_section_mappings as psm', function ($join) use ($userSectionIdList) {
                    $join->on('pm.new_colony_name', '=', 'psm.colony_id');
                    $join->whereColumn('pm.property_type', 'psm.property_type');
                    $join->whereColumn('pm.property_sub_type', 'psm.property_subtype');
                    $join->whereIn('psm.section_id', $userSectionIdList);
                });
            }, function ($query) {
                return $query->leftJoin('property_section_mappings as psm', function ($join) {
                    $join->on('pm.new_colony_name', '=', 'psm.colony_id');
                    $join->whereColumn('pm.property_type', 'psm.property_type');
                    $join->whereColumn('pm.property_sub_type', 'psm.property_subtype');
                });
            })
            ->join('sections', 'psm.section_id', '=', 'sections.id')
            ->select('sections.id', 'sections.name as section_name', DB::raw('count(pm.id) as number_of_properties'))
            ->groupBy('sections.id', 'sections.name');
        return $query->get();
    } */

    public function sectionwisePropertyCountNew($export, $page = 1)
    {
        list($filterUserSections, $userSectionIdList) = getUserAssignedSections();

        /* $query = DB::table('sections')
            ->leftJoin('property_section_mappings as psm', 'sections.id', '=', 'psm.section_id')
            ->leftJoin('property_masters as pm', function ($join) {
                $join->on('pm.new_colony_name', '=', 'psm.colony_id')
                    ->whereColumn('pm.property_type', 'psm.property_type')
                    ->whereColumn('pm.property_sub_type', 'psm.property_subtype');
            })
            ->leftJoin('splited_property_details as spd', 'pm.id', '=', 'spd.property_master_id')
            ->select(
                'sections.id', 
                'sections.name as section_name', 
                DB::raw('COUNT(DISTINCT pm.id) as number_of_properties')
            )
            ->groupBy('sections.id', 'sections.name')->orderBy('sections.name');
        if ($filterUserSections && !empty($userSectionIdList)) {
            $query->whereIn('sections.id', $userSectionIdList);
        } */
        $query = DB::table('property_masters', 'pm')
            ->leftJoin('splited_property_details as spd', 'pm.id', '=', 'spd.property_master_id')
            ->when($filterUserSections, function ($query) use ($userSectionIdList) {
                return $query->join('sections', function ($join) use ($userSectionIdList) {
                    $join->on('pm.section_code', '=', 'sections.section_code')
                        ->whereIn('sections.id', $userSectionIdList);
                });
            }, function ($query) {
                return $query->leftJoin('sections', 'pm.section_code', '=', 'sections.section_code');
            })
            ->select('sections.id', 'sections.name as section_name', DB::raw('count(pm.id) as number_of_properties'))
            // Filter out empty/null section names
            ->whereNotNull('sections.name')
            ->where('sections.name', '!=', '')
            // Order by section name
            ->orderBy('sections.name')
            ->groupBy('sections.id', 'sections.name');


        return $query->get();
    }

    /* public function propertyInASectionCountNew($section)
    {
        $query = DB::table('property_masters as pm')
            ->select(
                'pm.id',
                'pm.unique_propert_id',
                'pm.old_propert_id as property_id',
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN NULL ELSE spd.child_prop_id END AS child_prop_id'),
                'pm.property_type',
                'pm.property_sub_type',
                'pm.land_type',
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pm.status ELSE spd.property_status END AS property_status'),
                // 'sections.section_code as section',
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pld.presently_known_as ELSE spd.presently_known_as END AS address'),
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pld.plot_area_in_sqm ELSE spd.area_in_sqm END AS area_in_sqm'),
                'cld.lessees_name as current_lesse_name',
                'pld.doe as date_of_execution',
                'pm.is_joint_property'
            )
            ->leftJoin('property_lease_details as pld', 'pm.id', '=', 'pld.property_master_id')
            ->leftJoin('splited_property_details as spd', 'pm.id', '=', 'spd.property_master_id')
            ->leftJoin(DB::raw('(SELECT * FROM current_lessee_details WHERE flat_id IS NULL) as cld'), function ($join) {
                $join->on('pm.id', '=', 'cld.property_master_id')
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereNull('pm.is_joint_property')
                                ->whereNull('cld.splited_property_detail_id');
                        })->orWhere(function ($query) {
                            $query->whereNotNull('pm.is_joint_property')
                                ->whereColumn('spd.id', '=', 'cld.splited_property_detail_id');
                        });
                    });
            })
            ->leftJoin('items as item_type', 'item_type.id', '=', 'pm.property_type')
            ->leftJoin('items as latest_item_type', 'latest_item_type.id', '=', 'pld.property_type_at_present')
            ->leftJoin('items as land_type_names', 'land_type_names.id', '=', 'pm.land_type')
            ->leftJoin('items as item_sub_type', 'item_sub_type.id', '=', 'pm.property_sub_type')
            ->leftJoin('items as latest_item_sub_type', 'latest_item_sub_type.id', '=', 'pld.property_sub_type_at_present')
            ->leftJoin('items as item_property_status', function ($join) {
                $join->on('item_property_status.id', '=', DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pm.status ELSE spd.property_status END'));
            });

        // Section-specific or general join
        if ($section) {
            $query->join('property_section_mappings as psm', function ($join) use ($section) {
                $join->on('pm.new_colony_name', '=', 'psm.colony_id')
                    ->whereColumn('pm.property_type', 'psm.property_type')
                    ->whereColumn('pm.property_sub_type', 'psm.property_subtype')
                    ->where('psm.section_id', '=', $section);
            });
        } else {
            $query->leftJoin('property_section_mappings as psm', function ($join) {
                $join->on('pm.new_colony_name', '=', 'psm.colony_id')
                    ->whereColumn('pm.property_type', 'psm.property_type')
                    ->whereColumn('pm.property_sub_type', 'psm.property_subtype');
            });
        }

        // $query->leftJoin('sections', 'psm.section_id', '=', 'sections.id')
            $query->addSelect(
                'item_type.item_name as property_type',
                'item_sub_type.item_name as property_sub_type',
                'item_property_status.item_name as property_status',
                'land_type_names.item_name as land_type',
                'latest_item_type.item_name as latestPropertyType',
                'latest_item_sub_type.item_name as latestPropertySubType'
            );

        // Execute and format
        $results = $query->get()->map(function ($item) {
            $item->area_in_sqm = number_format((float) $item->area_in_sqm, 2, '.', '');
            $item->date_of_execution = $item->date_of_execution ? \Carbon\Carbon::parse($item->date_of_execution)->format('d/m/Y') : null;
            // $item->is_joint_property = $item->is_joint_property == 1 ? 'Yes' : 'No';
            return $item;
        });

        return $results;
    } */

    public function propertyInASectionCountNew($section)
    {
        $sectionCode = Section::where('id', $section)->value('section_code');
        if (!$sectionCode) {
            // Handle case where section ID is invalid
            return collect(); // Return empty collection
        }

        $query = DB::table('property_masters as pm')
            ->select(
                'pm.id',
                'pm.unique_propert_id as unique_propert_id',
                'pm.old_propert_id as property_id',
                'pm.section_code', // Ensure this is included
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN NULL ELSE spd.child_prop_id END AS child_prop_id'),
                'pm.property_type',
                'pm.property_sub_type',
                'pm.land_type',
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pm.status ELSE spd.property_status END AS property_status'),
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pld.presently_known_as ELSE spd.presently_known_as END AS address'),
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pld.plot_area_in_sqm ELSE spd.area_in_sqm END AS area_in_sqm'),
                'cld.lessees_name as current_lesse_name',
                'pld.doe as date_of_execution',
                'pm.is_joint_property',
                'item_type.item_name as property_type_name', // Changed alias to avoid conflict
                'item_sub_type.item_name as property_sub_type_name',
                'item_property_status.item_name as property_status_name',
                'land_type_names.item_name as land_type_name',
                'latest_item_type.item_name as latestPropertyType',
                'latest_item_sub_type.item_name as latestPropertySubType'
            )
            ->leftJoin('property_lease_details as pld', 'pm.id', '=', 'pld.property_master_id')
            ->leftJoin('splited_property_details as spd', 'pm.id', '=', 'spd.property_master_id')
            ->leftJoin(DB::raw('(SELECT * FROM current_lessee_details WHERE flat_id IS NULL) as cld'), function ($join) {
                $join->on('pm.id', '=', 'cld.property_master_id')
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereNull('pm.is_joint_property')
                                ->whereNull('cld.splited_property_detail_id');
                        })->orWhere(function ($query) {
                            $query->whereNotNull('pm.is_joint_property')
                                ->whereColumn('spd.id', '=', 'cld.splited_property_detail_id');
                        });
                    });
            })
            ->leftJoin('items as item_type', 'item_type.id', '=', 'pm.property_type')
            ->leftJoin('items as latest_item_type', 'latest_item_type.id', '=', 'pld.property_type_at_present')
            ->leftJoin('items as land_type_names', 'land_type_names.id', '=', 'pm.land_type')
            ->leftJoin('items as item_sub_type', 'item_sub_type.id', '=', 'pm.property_sub_type')
            ->leftJoin('items as latest_item_sub_type', 'latest_item_sub_type.id', '=', 'pld.property_sub_type_at_present')
            ->leftJoin('items as item_property_status', function ($join) {
                $join->on('item_property_status.id', '=', DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pm.status ELSE spd.property_status END'));
            });

        // Add section_code filter if provided
        if ($sectionCode) {
            $query->where('pm.section_code', $sectionCode);
        }

        $query->orderBy('pm.unique_propert_id', 'asc');

        // Return as collection (not array)
        $results = $query->get();

        // If you need formatted data for DataTables
        $formattedResults = $results->map(function ($item) {
            return [
                'id' => $item->id,
                'unique_propert_id' => $item->unique_propert_id,
                'property_id' => $item->property_id,
                'section_code' => $item->section_code,
                'child_prop_id' => $item->child_prop_id,
                'property_type' => $item->property_type_name,
                'property_sub_type' => $item->property_sub_type_name,
                'land_type' => $item->land_type_name,
                'property_status' => $item->property_status_name,
                'address' => $item->address,
                'area_in_sqm' => number_format((float) $item->area_in_sqm, 2, '.', ''),
                'current_lesse_name' => $item->current_lesse_name,
                'date_of_execution' => $item->date_of_execution
                    ? \Carbon\Carbon::parse($item->date_of_execution)->format('d/m/Y')
                    : null,
                'is_joint_property' => $item->is_joint_property,
                'property_type_name' => $item->property_type_name,
                'property_sub_type_name' => $item->property_sub_type_name,
                'property_status_name' => $item->property_status_name,
                'land_type_name' => $item->land_type_name,
                'latestPropertyType' => $item->latestPropertyType,
                'latestPropertySubType' => $item->latestPropertySubType,
            ];
        });

        return $formattedResults;
    }


    public function colonyWisePropertyReport($export, $page = 1)
    {
        list($filterUserSections, $userSectionIdList) = getUserAssignedSections();
        $colonyNames = [];
        if (!$export) {
            $colonies = DB::table('property_masters as pm')
                ->when($filterUserSections, function ($query) use ($userSectionIdList) {
                    return $query->join('property_section_mappings as psm', function ($join) use ($userSectionIdList) {
                        $join->on('pm.new_colony_name', '=', 'psm.colony_id');
                        $join->whereColumn('pm.property_type', 'psm.property_type');
                        $join->whereColumn('pm.property_sub_type', 'psm.property_subtype');
                        $join->whereIn('psm.section_id', $userSectionIdList);
                    });
                })
                ->leftJoin('old_colonies as colony', 'pm.new_colony_name', '=', 'colony.id')
                ->select('pm.new_colony_name as colony_name')
                ->groupBy('pm.new_colony_name')
                ->orderBy('colony.name')
                ->paginate(50, ['*'], 'page', $page);
            $colonyNames = $colonies->pluck('colony_name')->toArray();
        }

        $data = DB::table('property_masters as pm')
            ->leftJoin('splited_property_details as spd', 'pm.id', '=', 'spd.property_master_id')
            ->leftJoin('old_colonies as colony', 'pm.new_colony_name', '=', 'colony.id')
            ->join('items', function ($join) {
                $join->on(DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pm.status ELSE spd.property_status END'), '=', 'items.id');
            })
            ->select(
                'colony.name as colony_name',
                'items.item_name as property_status',
                DB::raw('COUNT(pm.id) as property_count')
            )
            ->when(!empty($colonyNames), function ($query) use ($colonyNames) {
                return $query->whereIn('pm.new_colony_name', $colonyNames);
            })
            ->groupBy('colony.name', 'items.item_name')
            ->get();

        $formattedData = [];
        foreach ($data as $row) {
            if (!isset($formattedData[$row->colony_name])) {
                $formattedData[$row->colony_name] = ['Colony Name' => $row->colony_name, 'Lease Hold' => 0, 'Free Hold' => 0, 'Unallotted' => 0, 'Total' => 0];
            }
            $formattedData[$row->colony_name][$row->property_status] = $row->property_count;
            $formattedData[$row->colony_name]['Total'] += $row->property_count;
        }

        return $export ? $formattedData : [
            'headerValues' => ['Colony Name', 'Lease Hold', 'Free Hold', 'Unallotted', 'Total'],
            'dataValues' => $formattedData,
            'pagination' => [
                'total' => $colonies->total(),
                'per_page' => $colonies->perPage(),
                'current_page' => $colonies->currentPage(),
                'last_page' => $colonies->lastPage(),
            ]
        ];
    }

    public function colonyWisePropertyReportNew($search = null)
    {
        $query = DB::table('property_masters as pm')
            ->leftJoin('splited_property_details as spd', 'pm.id', '=', 'spd.property_master_id')
            ->leftJoin('old_colonies as colony', 'pm.new_colony_name', '=', 'colony.id')
            ->join('items', function ($join) {
                $join->on(DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pm.status ELSE spd.property_status END'), '=', 'items.id');
            })
            ->select(
                'colony.name as colony_name',
                DB::raw('SUM(CASE WHEN items.item_name = "Lease Hold" THEN 1 ELSE 0 END) as lease_hold_count'),
                DB::raw('SUM(CASE WHEN items.item_name = "Free Hold" THEN 1 ELSE 0 END) as free_hold_count'),
                DB::raw('SUM(CASE WHEN items.item_name = "Unallotted" THEN 1 ELSE 0 END) as unallotted_count'),
                DB::raw('COUNT(pm.id) as total_count')
            )
            ->groupBy('colony.name');

        // Apply search filter if provided
        if (!empty($search)) {
            $query->havingRaw('
            colony_name LIKE ? OR
            lease_hold_count = ? OR
            free_hold_count = ? OR
            unallotted_count = ? OR
            total_count = ?
        ', [
                "%{$search}%",
                (int)$search,
                (int)$search,
                (int)$search,
                (int)$search
            ]);
        }

        return $query->get();
    }

    public function typewisePropertyCount($export)
    {
        list($filterUserSections, $userSectionIdList) = getUserAssignedSections();
        /* $data = DB::table('property_masters', 'pm')
            ->when($filterUserSections, function ($query) use ($userSectionIdList) {
                return $query->join('property_section_mappings as psm', function ($join) use ($userSectionIdList) {
                    $join->on('pm.new_colony_name', '=', 'psm.colony_id');
                    $join->whereColumn('pm.property_type', 'psm.property_type');
                    $join->whereColumn('pm.property_sub_type', 'psm.property_subtype');
                    $join->whereIn('psm.section_id', $userSectionIdList);
                });
            })
            ->leftJoin('splited_property_details as spd', 'pm.id', '=', 'spd.property_master_id')
            ->leftJoin('items', 'pm.property_type', '=', 'items.id')
            ->select(
                DB::raw('coalesce(items.item_name, \'Unallotted\') as property_type'),
                DB::raw('count(pm.id) as number_of_properties')
            )
            ->groupBy('pm.property_type', 'items.item_name')
            ->orderByRaw("CASE WHEN items.item_name IS NULL THEN 1 ELSE 0 END, items.item_order")
            ->get(); */
        $data = DB::table('property_masters as pm')
            ->when($filterUserSections, function ($data) use ($userSectionIdList) {
                return $data->join('sections', function ($join) use ($userSectionIdList) {
                    $join->on('pm.section_code', '=', 'sections.section_code')
                        ->whereIn('sections.id', $userSectionIdList);
                });
            })
            ->leftJoin('splited_property_details as spd', 'pm.id', '=', 'spd.property_master_id')
            ->leftJoin('items', 'pm.property_type', '=', 'items.id')
            ->whereNotNull('items.item_name')
            ->select(
                'items.item_name as property_type',
                'items.item_order',
                DB::raw('COUNT(pm.id) as number_of_properties')
            )
            ->groupBy('items.item_name', 'items.item_order')
            ->orderBy('items.item_order')
            ->get();
        return $export ? $data :  [
            'headerValues' => ['property_type', 'number_of_properties'],
            'dataValues' => $data,
        ];
    }

    public function typewisePropertyCountNew()
    {
        list($filterUserSections, $userSectionIdList) = getUserAssignedSections();
        /* $query = DB::table('property_masters', 'pm')
            ->when($filterUserSections, function ($query) use ($userSectionIdList) {
                return $query->join('property_section_mappings as psm', function ($join) use ($userSectionIdList) {
                    $join->on('pm.new_colony_name', '=', 'psm.colony_id');
                    $join->whereColumn('pm.property_type', 'psm.property_type');
                    $join->whereColumn('pm.property_sub_type', 'psm.property_subtype');
                    $join->whereIn('psm.section_id', $userSectionIdList);
                });
            })
            ->leftJoin('splited_property_details as spd', 'pm.id', '=', 'spd.property_master_id')
            ->leftJoin('items', 'pm.property_type', '=', 'items.id')
            ->select(
                DB::raw('coalesce(items.item_name, \'Unallotted\') as property_type'),
                DB::raw('count(pm.id) as number_of_properties')
            )
            ->groupBy('pm.property_type', 'items.item_name')
            ->orderByRaw("CASE WHEN items.item_name IS NULL THEN 1 ELSE 0 END, items.item_order")
            ->get(); */
        /* $query = DB::table('property_masters', 'pm')
            ->when($filterUserSections, function ($query) use ($userSectionIdList) {
                return $query->join('sections', function ($join) use ($userSectionIdList) {
                    $join->on('pm.section_code', '=', 'sections.section_code')
                        ->whereIn('sections.id', $userSectionIdList);
                });
            })
            ->leftJoin('splited_property_details as spd', 'pm.id', '=', 'spd.property_master_id')
            ->leftJoin('items', 'pm.property_type', '=', 'items.id')
            ->select(
                DB::raw('coalesce(items.item_name, \'Unallotted\') as property_type'),
                DB::raw('count(pm.id) as number_of_properties')
            )
            ->groupBy('pm.property_type', 'items.item_name')
            ->orderByRaw("CASE WHEN items.item_name IS NULL THEN 1 ELSE 0 END, items.item_order")
            ->get(); */
        $query = DB::table('property_masters as pm')
            ->when($filterUserSections, function ($query) use ($userSectionIdList) {
                return $query->join('sections', function ($join) use ($userSectionIdList) {
                    $join->on('pm.section_code', '=', 'sections.section_code')
                        ->whereIn('sections.id', $userSectionIdList);
                });
            })
            ->leftJoin('splited_property_details as spd', 'pm.id', '=', 'spd.property_master_id')
            ->leftJoin('items', 'pm.property_type', '=', 'items.id')
            ->whereNotNull('items.item_name')
            ->select(
                'items.item_name as property_type',
                'items.item_order',
                DB::raw('COUNT(pm.id) as number_of_properties')
            )
            ->groupBy(
                'items.item_name',
                'items.item_order'
            )
            ->orderBy('items.item_order')
            ->get();
        return $query;
    }


    /* public function typewisePropertyCount($export)
    {
        list($filterUserSections, $userSectionIdList) = getUserAssignedSections();
        $data = DB::table('property_masters', 'pm')
            ->when($filterUserSections, function ($query) use ($userSectionIdList) {
                return $query->join('property_section_mappings as psm', function ($join) use ($userSectionIdList) {
                    $join->on('pm.new_colony_name', '=', 'psm.colony_id');
                    $join->whereColumn('pm.property_type', 'psm.property_type');
                    $join->whereColumn('pm.property_sub_type', 'psm.property_subtype');
                    $join->whereIn('psm.section_id', $userSectionIdList);
                });
            })
            ->leftJoin('splited_property_details as spd', 'pm.id', '=', 'spd.property_master_id')
            ->leftJoin('items', 'pm.property_type', '=', 'items.id')
            ->select(
                DB::raw('coalesce(items.item_name, \'Unallotted\') as property_type'),
                DB::raw('count(pm.id) as number_of_properties')
            )
            ->groupBy('pm.property_type', 'items.item_name')
            ->orderByRaw("CASE WHEN items.item_name IS NULL THEN 1 ELSE 0 END, items.item_order")
            ->get();
        return $export ? $data :  [
            'headerValues' => ['property_type', 'number_of_properties'],
            'dataValues' => $data,
        ];
    }

    public function typewisePropertyCountNew()
    {
        list($filterUserSections, $userSectionIdList) = getUserAssignedSections();
        // $query = DB::table('property_masters', 'pm')
        //     ->when($filterUserSections, function ($query) use ($userSectionIdList) {
        //         return $query->join('property_section_mappings as psm', function ($join) use ($userSectionIdList) {
        //             $join->on('pm.new_colony_name', '=', 'psm.colony_id');
        //             $join->whereColumn('pm.property_type', 'psm.property_type');
        //             $join->whereColumn('pm.property_sub_type', 'psm.property_subtype');
        //             $join->whereIn('psm.section_id', $userSectionIdList);
        //         });
        //     })
        //     ->leftJoin('splited_property_details as spd', 'pm.id', '=', 'spd.property_master_id')
        //     ->leftJoin('items', 'pm.property_type', '=', 'items.id')
        //     ->select(
        //         DB::raw('coalesce(items.item_name, \'Unallotted\') as property_type'),
        //         DB::raw('count(pm.id) as number_of_properties')
        //     )
        //     ->groupBy('pm.property_type', 'items.item_name')
        //     ->orderByRaw("CASE WHEN items.item_name IS NULL THEN 1 ELSE 0 END, items.item_order")
        //     ->get();
        $query = DB::table('property_masters as pm')
    ->when($filterUserSections, function ($query) use ($userSectionIdList) {
        return $query->join('sections', function ($join) use ($userSectionIdList) {
            $join->on('pm.section_code', '=', 'sections.section_code')
                 ->whereIn('sections.id', $userSectionIdList);
        });
    })
    ->leftJoin('splited_property_details as spd', 'pm.id', '=', 'spd.property_master_id')
    ->leftJoin('items', 'pm.property_type', '=', 'items.id')
    ->whereNotNull('items.item_name')
    ->select(
        'items.item_name as property_type',
        'items.item_order',
        DB::raw('COUNT(pm.id) as number_of_properties')
    )
    ->groupBy('items.item_name', 'items.item_order')
    ->orderBy('items.item_order')
    ->get();

        return $query;
    } */

    //Add new method for report type Property In A Section - Lalit (11/March/2025)
    /* public function propertyInASectionCount($export, $section, $page = 1)
    {
        $query = DB::table('property_masters as pm')
            ->select(
                'pm.id',
                'pm.unique_propert_id',
                'pm.old_propert_id as property_id',
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN NULL ELSE spd.child_prop_id END AS child_prop_id'),
                'pm.property_type',
                'pm.property_sub_type',
                'pm.land_type',
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pm.status ELSE spd.property_status END AS property_status'),
                'pm.file_no',
                // 'sections.section_code as section',
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pld.presently_known_as ELSE spd.presently_known_as END AS address'),
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pld.plot_area_in_sqm ELSE spd.area_in_sqm END AS area_in_sqm'),
                'cld.lessees_name as current_lesse_name',
                'pld.doe as date_of_execution',
                'pm.is_joint_property'
            )
            ->leftJoin('property_lease_details as pld', 'pm.id', '=', 'pld.property_master_id')
            ->leftJoin('splited_property_details as spd', 'pm.id', '=', 'spd.property_master_id')
            ->leftJoin(DB::raw('(select * from current_lessee_details where flat_id is null) as cld'), function ($join) {
                $join->on('pm.id', '=', 'cld.property_master_id')
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereNull('pm.is_joint_property')
                                ->whereNull('cld.splited_property_detail_id');
                        })->orWhere(function ($query) {
                            $query->whereNotNull('pm.is_joint_property')
                                ->whereColumn('spd.id', '=', 'cld.splited_property_detail_id');
                        });
                    });
            });

        // Join items table for property_type, property_sub_type, and property_status
        $query->leftJoin('items as item_type', 'item_type.id', '=', 'pm.property_type')
            ->leftJoin('items as latest_item_type', 'latest_item_type.id', '=', 'pld.property_type_at_present')
            ->leftJoin('items as land_type_names', 'land_type_names.id', '=', 'pm.land_type')
            ->leftJoin('items as item_sub_type', 'item_sub_type.id', '=', 'pm.property_sub_type')
            ->leftJoin('items as latest_item_sub_type', 'latest_item_sub_type.id', '=', 'pld.property_sub_type_at_present')
            ->leftJoin('items as item_property_status', function ($join) {
                $join->on('item_property_status.id', '=', DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pm.status ELSE spd.property_status END'));
            })
            ->when($section, function ($query) use ($section) {
                return $query->join('property_section_mappings as psm', function ($join) use ($section) {
                    $join->on('pm.new_colony_name', '=', 'psm.colony_id');
                    $join->whereColumn('pm.property_type', 'psm.property_type');
                    $join->whereColumn('pm.property_sub_type', 'psm.property_subtype');
                    $join->where('psm.section_id', $section);
                });
            }, function ($query) {
                return $query->leftJoin('property_section_mappings as psm', function ($join) {
                    $join->on('pm.new_colony_name', '=', 'psm.colony_id');
                    $join->whereColumn('pm.property_type', 'psm.property_type');
                    $join->whereColumn('pm.property_sub_type', 'psm.property_subtype');
                });
            })
            ->leftJoin('sections', 'psm.section_id', 'sections.id');

        // Select item names
        $query->addSelect(
            'item_type.item_name as property_type',
            'item_sub_type.item_name as property_sub_type',
            'item_property_status.item_name as property_status',
            'land_type_names.item_name as land_type',
            'latest_item_type.item_name as latestPropertyType',
            'latest_item_sub_type.item_name as latestPropertySubType',
        );

        if ($export) {
            return $query->get();
        } else {
            $paginated = $query->paginate(50, ['*'], 'page', $page);
            // Format values
            $formattedData = collect($paginated->items())->map(function ($item) {
                $item->{"area_in_sqm"} = number_format((float) $item->{"area_in_sqm"}, 2, '.', ''); // Ensures two decimals without commas
                $item->date_of_execution = $item->date_of_execution ? \Carbon\Carbon::parse($item->date_of_execution)->format('d/m/Y') : null; // Format date_of_execution
                $item->is_joint_property = $item->is_joint_property == 1 ? "Yes" : "No"; // Convert is_joint_property to Yes/No
                return $item;
            });

            return [
                'headerValues' => ['property_id', 'unique_propert_id', 'land_type', 'property_status', 'property_type', 'property_sub_type', 'area_in_sqm', 'date_of_execution', 'current_lesse_name', 'is_joint_property'],
                'dataValues' => $formattedData,
                'pagination' => [
                    'total' => $paginated->total(),
                    'per_page' => $paginated->perPage(),
                    'current_page' => $paginated->currentPage(),
                    'last_page' => $paginated->lastPage(),
                ]
            ];
        }
    } */

    public function propertyInASectionCount($export, $section, $page = 1)
    {
        // Get section_code if section ID is provided
        $sectionCode = $section ? Section::where('id', $section)->value('section_code') : null;

        $query = DB::table('property_masters as pm')
            ->select(
                'pm.id',
                'pm.unique_propert_id',
                'pm.old_propert_id as property_id',
                'pm.section_code',
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN NULL ELSE spd.child_prop_id END AS child_prop_id'),
                'pm.property_type',
                'pm.property_sub_type',
                'pm.land_type',
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pm.status ELSE spd.property_status END AS property_status'),
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pld.presently_known_as ELSE spd.presently_known_as END AS address'),
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pld.plot_area_in_sqm ELSE spd.area_in_sqm END AS area_in_sqm'),
                'cld.lessees_name as current_lesse_name',
                'pld.doe as date_of_execution',
                'pm.is_joint_property',
                'item_type.item_name as property_type_name',
                'item_sub_type.item_name as property_sub_type_name',
                'item_property_status.item_name as property_status_name',
                'land_type_names.item_name as land_type_name',
                'latest_item_type.item_name as latestPropertyType',
                'latest_item_sub_type.item_name as latestPropertySubType'
            )
            ->leftJoin('property_lease_details as pld', 'pm.id', '=', 'pld.property_master_id')
            ->leftJoin('splited_property_details as spd', 'pm.id', '=', 'spd.property_master_id')
            ->leftJoin(DB::raw('(select * from current_lessee_details where flat_id is null) as cld'), function ($join) {
                $join->on('pm.id', '=', 'cld.property_master_id')
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereNull('pm.is_joint_property')
                                ->whereNull('cld.splited_property_detail_id');
                        })->orWhere(function ($query) {
                            $query->whereNotNull('pm.is_joint_property')
                                ->whereColumn('spd.id', '=', 'cld.splited_property_detail_id');
                        });
                    });
            })
            ->leftJoin('items as item_type', 'item_type.id', '=', 'pm.property_type')
            ->leftJoin('items as latest_item_type', 'latest_item_type.id', '=', 'pld.property_type_at_present')
            ->leftJoin('items as land_type_names', 'land_type_names.id', '=', 'pm.land_type')
            ->leftJoin('items as item_sub_type', 'item_sub_type.id', '=', 'pm.property_sub_type')
            ->leftJoin('items as latest_item_sub_type', 'latest_item_sub_type.id', '=', 'pld.property_sub_type_at_present')
            ->leftJoin('items as item_property_status', function ($join) {
                $join->on('item_property_status.id', '=', DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pm.status ELSE spd.property_status END'));
            });

        // Apply section_code filter if provided
        if ($sectionCode) {
            $query->where('pm.section_code', $sectionCode);
        }

        // Apply ordering
        $query->orderBy('pm.unique_propert_id', 'asc');

        if ($export) {
            // For export - return formatted data with ALL fields needed for export
            $results = $query->get();

            return $results->map(function ($item) {
                return [
                    'id' => $item->id,
                    'unique_propert_id' => $item->unique_propert_id,
                    'property_id' => $item->property_id,
                    'section_code' => $item->section_code,
                    'child_prop_id' => $item->child_prop_id,
                    // Include both IDs and Names for flexibility
                    'property_type' => $item->property_type,
                    'property_type_name' => $item->property_type_name,
                    'property_sub_type' => $item->property_sub_type,
                    'property_sub_type_name' => $item->property_sub_type_name,
                    'land_type' => $item->land_type,
                    'land_type_name' => $item->land_type_name,
                    'property_status' => $item->property_status,
                    'property_status_name' => $item->property_status_name,
                    'address' => $item->address,
                    'area_in_sqm' => number_format((float) $item->area_in_sqm, 2, '.', ''),
                    'current_lesse_name' => $item->current_lesse_name,
                    'date_of_execution' => $item->date_of_execution
                        ? \Carbon\Carbon::parse($item->date_of_execution)->format('d/m/Y')
                        : null,
                    'is_joint_property' => $item->is_joint_property,
                    'is_joint_property_text' => $item->is_joint_property == 1 ? "Yes" : "No",
                    'latestPropertyType' => $item->latestPropertyType,
                    'latestPropertySubType' => $item->latestPropertySubType,
                ];
            });
        } else {
            // For paginated view - return array format
            $paginated = $query->paginate(50, ['*'], 'page', $page);

            // Format values
            $formattedData = collect($paginated->items())->map(function ($item) {
                return [
                    'id' => $item->id,
                    'unique_propert_id' => $item->unique_propert_id,
                    'property_id' => $item->property_id,
                    'section_code' => $item->section_code,
                    'child_prop_id' => $item->child_prop_id,
                    'property_type' => $item->property_type,
                    'property_sub_type' => $item->property_sub_type,
                    'land_type' => $item->land_type,
                    'property_status' => $item->property_status,
                    'address' => $item->address,
                    'area_in_sqm' => number_format((float) $item->area_in_sqm, 2, '.', ''),
                    'current_lesse_name' => $item->current_lesse_name,
                    'date_of_execution' => $item->date_of_execution
                        ? \Carbon\Carbon::parse($item->date_of_execution)->format('d/m/Y')
                        : null,
                    'is_joint_property' => $item->is_joint_property == 1 ? "Yes" : "No",
                    'property_type_name' => $item->property_type_name,
                    'property_sub_type_name' => $item->property_sub_type_name,
                    'property_status_name' => $item->property_status_name,
                    'land_type_name' => $item->land_type_name,
                    'latestPropertyType' => $item->latestPropertyType,
                    'latestPropertySubType' => $item->latestPropertySubType,
                ];
            });

            return [
                'headerValues' => [
                    'property_id',
                    'unique_propert_id',
                    'section_code',
                    'land_type_name',
                    'property_status_name',
                    'property_type_name',
                    'property_sub_type_name',
                    'area_in_sqm',
                    'date_of_execution',
                    'current_lesse_name',
                    'is_joint_property',
                    'address'
                ],
                'dataValues' => $formattedData,
                'pagination' => [
                    'total' => $paginated->total(),
                    'per_page' => $paginated->perPage(),
                    'current_page' => $paginated->currentPage(),
                    'last_page' => $paginated->lastPage(),
                ],
                'section_code' => $sectionCode,
                'section_id' => $section
            ];
        }
    }

    public function colonyWiseFilterResults($filter = [], $withLimit = true)
    {
        if ($withLimit) {
            $limit = isset($filter['limit']) ? $filter['limit'] : 50;
            $pageNum = isset($filter['page']) ? $filter['page'] : 1;
            $offset = ($pageNum - 1) * $limit;
        }

        $userSectionsIdList = $filter['section_id'];
        unset($filter['section_id']);

        /**lesse name filter */
        if (isset($filter['name'])) {
            $where[] = "lesse_name like '%" . $filter['name'] . "%'";
        }
        /**contact filter */
        if (isset($filter['contact'])) {
            $where[] = "phone_no like '" . $filter['contact'] . "%'";
        }

        /** property inspection details */ //outstandingDues
        if (isset($filter['outstandingDues'])) {
            $min = $filter['outstandingDues']['min'];
            $max = $filter['outstandingDues']['max'];
            if ($min != "" && $max != "") {
                $totalDues = "total_dues between $min and $max";
            } else {
                if ($min != "") {
                    $totalDues = "total_dues >= $min ";
                }
                if ($max != "") {
                    $totalDues = "total_dues <= $max ";
                }
            }
            $where[] = $totalDues;
        }

        $query = DB::table('property_masters as pm')
            ->leftJoin('splited_property_details as spd', 'pm.id', '=', 'spd.property_master_id')
            ->leftJoin(DB::raw('(select *,case when date_of_expiration is null then null else timestampdiff(year, doe, date_of_expiration ) end as lease_tenure from property_lease_details) as pld'), 'pm.id', '=', 'pld.property_master_id')
            ->leftJoin('old_colonies as oc', 'pm.new_colony_name', '=', 'oc.id')
            ->{isset($filter['reEnteredSince']) ? 'join' : 'leftJoin'}('property_misc_details as pmd', function ($join) {
                $join->on('pm.id', '=', 'pmd.property_master_id')
                    ->where(function ($query) {
                        $query->where(function ($q) {
                            $q->whereNull('pm.is_joint_property')
                                ->whereNull('pmd.splited_property_detail_id');
                        })->orWhere(function ($q) {
                            $q->whereNotNull('pm.is_joint_property')
                                ->whereColumn('pmd.splited_property_detail_id', '=', 'spd.id');
                        });
                    });
            })
            ->leftJoin(DB::raw('(select * from current_lessee_details where flat_id is null) as cld'), function ($join) {
                $join->on('pm.id', '=', 'cld.property_master_id')
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereNull('pm.is_joint_property')
                                ->whereNull('cld.splited_property_detail_id');
                        })->orWhere(function ($query) {
                            $query->whereNotNull('pm.is_joint_property')
                                ->whereColumn('spd.id', '=', 'cld.splited_property_detail_id');
                        });
                    });
            })
            ->leftJoin('property_inspection_demand_details as pid', function ($join) {
                $join->on('pm.id', '=', 'pid.property_master_id')
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereNull('pm.is_joint_property')
                                ->whereNull('pid.splited_property_detail_id');
                        })->orWhere(function ($query) {
                            $query->whereNotNull('pm.is_joint_property')
                                ->whereColumn('spd.id', '=', 'pid.splited_property_detail_id');
                        });
                    });
            })
            ->leftJoin('property_contact_details as pcd', function ($join) {
                $join->on('pm.id', '=', 'pcd.property_master_id')
                    ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereNull('pm.is_joint_property')
                                ->whereNull('pcd.splited_property_detail_id');
                        })->orWhere(function ($query) {
                            $query->whereNotNull('pm.is_joint_property')
                                ->whereColumn('spd.id', '=', 'pcd.splited_property_detail_id');
                        });
                    });
            })
            ->select(
                // 'pm.id',
                // 'pm.old_propert_id',
                // 'pm.unique_propert_id',
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pm.id ELSE spd.property_master_id END AS id'),
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pm.old_propert_id ELSE spd.old_property_id END AS old_propert_id'),
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pm.unique_propert_id ELSE spd.child_prop_id END AS unique_propert_id'),
                DB::raw('CASE WHEN pm.is_joint_property = 1 THEN "true" ELSE "false" END'),
                'pm.file_no',
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN "" ELSE spd.child_prop_id END AS child_id'),
                'pm.land_type',
                'pm.new_colony_name',
                'pm.block_no',
                'pm.plot_or_property_no',
                'pm.property_type',
                'pm.property_sub_type',
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pm.status ELSE spd.property_status END AS property_status'),
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pld.plot_area ELSE spd.current_area END AS input_area'),
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pld.unit ELSE spd.unit END AS unit'),
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pld.plot_area_in_sqm ELSE spd.area_in_sqm END AS area_in_sqm'),
                DB::raw("CASE WHEN pmd.is_gr_revised_ever = 1 THEN 'yes' ELSE 'No' END AS gr"),
                'cld.lessees_name as current_lessee_name',
                DB::raw('case when date_of_expiration is null then null else timestampdiff(year, doe, date_of_expiration ) end as lease_tenure'),
                'pld.doe',
                'pld.doa',
                'pld.date_of_expiration',
                'pld.type_of_lease',
                DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pld.plot_value_cr ELSE spd.plot_value_cr END AS land_value'),
                DB::raw('case when pm.is_joint_property is null then pld.presently_known_as else spd.presently_known_as end as address'),
                DB::raw('coalesce(pid.total_dues,0) as total_dues'),
                'pmd.is_re_rented',
                'pmd.re_rented_date',
                'pcd.phone_no',
                DB::raw('CASE 
                WHEN pm.is_joint_property IS NULL THEN COALESCE(pld.present_ground_rent, CAST(pld.gr_in_re_rs AS DECIMAL(12,2))) 
                ELSE spd.present_ground_rent 
                END AS gr_in_re_rs'),
                'oc.name as colony',
                'oc.id as colony_id',
                'pm.additional_remark as remarks'
            )
            ->when(isset($filter['land_type']), fn($q) => $q->where('pm.land_type', $filter['land_type']))

            ->when(
                !empty($filter['property_type']),
                fn($q) =>
                $q->whereIn('pm.property_type', $filter['property_type'])
            )

            ->when(
                !empty($filter['property_sub_type']),
                fn($q) =>
                $q->whereIn('pm.property_sub_type', $filter['property_sub_type'])
            )

            ->when(
                !empty($filter['land_status']),
                function ($q) use ($filter) {
                    $case = DB::raw("CASE WHEN pm.is_joint_property IS NULL THEN pm.status ELSE spd.property_status END");
                    return $q->whereIn($case, $filter['land_status']);
                }
            )

            ->when(
                !empty($filter['colony']),
                fn($q) =>
                $q->whereIn('new_colony_name', $filter['colony'])
            )

            // Apply all other common filters first
            ->when(
                !empty($userSectionsIdList),
                fn($q) =>
                $q->whereIn('section_code', $userSectionsIdList)
            )

            ->when(
                !empty($filter['propertyId']),
                fn($q) =>
                $q->where('old_propert_id', 'like', $filter['propertyId'] . '%')
            )

            ->when(!empty($filter['land_size']), function ($q) use ($filter) {
                $min = (float) $filter['land_size']['min'];
                $max = (float) $filter['land_size']['max'];

                $case = DB::raw("CAST(
                CASE
                    WHEN pm.is_joint_property IS NULL THEN pld.plot_area_in_sqm
                    ELSE spd.area_in_sqm
                END AS DECIMAL(10,2))");
                if ($min > 0 && $max > 0) {
                    $q->whereBetween($case, [$min, $max]);
                } elseif ($min > 0) {
                    $q->where($case, '>=', $min);
                } elseif ($max > 0) {
                    $q->where($case, '<=', $max);
                }
            })

            ->when(!empty($filter['date_of_execution']), function ($q) use ($filter) {
                $min = $filter['date_of_execution']['min'] ?? "";
                $max = $filter['date_of_execution']['max'] ?? "";
                if ($min !== "" && $max !== "") {
                    $q->whereBetween('doe', [$min, $max]);
                } elseif ($min !== "") {
                    $q->where('doe', '>=', $min);
                } elseif ($max !== "") {
                    $q->where('doe', '<=', $max);
                }
            })
            ->when(!empty($filter['date_of_allottment']), function ($q) use ($filter) {
                $min = $filter['date_of_allottment']['min'] ?? "";
                $max = $filter['date_of_allottment']['max'] ?? "";

                if ($min !== "" && $max !== "") {
                    $q->whereBetween('doa', [$min, $max]);
                } elseif ($min !== "") {
                    $q->where('doa', '>=', $min);
                } elseif ($max !== "") {
                    $q->where('doa', '<=', $max);
                }
            })
            ->when(!empty($filter['date_of_expiration']), function ($q) use ($filter) {
                $min = $filter['date_of_expiration']['min'] ?? "";
                $max = $filter['date_of_expiration']['max'] ?? "";

                if ($min !== "" && $max !== "") {
                    $q->whereBetween('date_of_expiration', [$min, $max]);
                } elseif ($min !== "") {
                    $q->where('date_of_expiration', '>=', $min);
                } elseif ($max !== "") {
                    $q->where('date_of_expiration', '<=', $max);
                }
            })
            ->when(!empty($filter['land_value']), function ($q) use ($filter) {
                $min = (float) $filter['land_value']['min'];
                $max = (float) $filter['land_value']['max'];

                $case = DB::raw("CAST(
                CASE
                    WHEN pm.is_joint_property IS NULL THEN pld.plot_value_cr
                    ELSE spd.plot_value_cr
                END AS DECIMAL(10,2))");
                if ($min > 0 && $max > 0) {
                    $q->whereBetween($case, [$min, $max]);
                } elseif ($min > 0) {
                    $q->where($case, '>=', $min);
                } elseif ($max > 0) {
                    $q->where($case, '<=', $max);
                }
            })

            ->when(
                !empty($filter['leaseDeed']),
                fn($q) =>
                $q->whereIn('type_of_lease', $filter['leaseDeed'])
            )

            ->when(
                !empty($filter['propertyAddress']),
                fn($q) =>
                $q->where('address', 'like', '%' . $filter['propertyAddress'] . '%')
            )

            ->when(!empty($filter['groundRent']), function ($q) use ($filter) {
                $min = $filter['groundRent']['min'] ?? "";
                $max = $filter['groundRent']['max'] ?? "";

                if ($min !== "" && $max !== "") {
                    $q->whereBetween('gr_in_re_rs', [$min, $max]);
                } elseif ($min !== "") {
                    $q->where('gr_in_re_rs', '>=', $min);
                } elseif ($max !== "") {
                    $q->where('gr_in_re_rs', '<=', $max);
                }
            })
            ->when(
                !empty($filter['name']),
                fn($q) =>
                $q->where('cld.lessees_name', 'like', '%' . $filter['name'] . '%')
            )

            ->when(
                !empty($filter['contact']),
                fn($q) =>
                $q->where('phone_no', 'like', $filter['contact'] . '%')
            )

            ->when(!empty($filter['outstandingDues']), function ($q) use ($filter) {
                $min = (float) $filter['outstandingDues']['min'];
                $max = (float) $filter['outstandingDues']['max'];

                $condition = DB::raw("CAST(pid.total_dues AS DECIMAL(10,2))");
                if ($min > 0 && $max > 0) {
                    $q->whereBetween($condition, [$min, $max]);
                } elseif ($min > 0) {
                    $q->where($condition, '>=', $min);
                } elseif ($max > 0) {
                    $q->where($condition, '<=', $max);
                }
            })
            ->when(isset($filter['leaseTenure']), function ($query) use ($filter) {
                $query->where(function ($q) use ($filter) {
                    foreach ($filter['leaseTenure'] as $range) {
                        switch ($range) {
                            case 'Perpetual':
                                $q->orWhereNull('pld.lease_tenure');
                                break;
                            case '0 - 5':
                                $q->orWhereBetween('pld.lease_tenure', [0, 5]);
                                break;
                            case '5 - 25':
                                $q->orWhereBetween('pld.lease_tenure', [5, 25]);
                                break;
                            case '25 - 50':
                                $q->orWhereBetween('pld.lease_tenure', [25, 50]);
                                break;
                            case '50 - 75':
                                $q->orWhereBetween('pld.lease_tenure', [50, 75]);
                                break;
                            case '75+':
                                $q->orWhere('pld.lease_tenure', '>', 75);
                                break;
                        }
                    }
                });
            })
            ->when(isset($filter['reEnteredSince']), function ($query) use ($filter) {
                $query->where(function ($q) use ($filter) {
                    foreach ($filter['reEnteredSince'] as $range) {
                        switch ($range) {
                            case 'y5+':
                                $q->orWhereRaw('DATEDIFF(CURDATE(), pmd.re_rented_date) > ?', [5 * 365]);
                                break;
                            case '1y - 5y':
                                $q->orWhereRaw('DATEDIFF(CURDATE(), pmd.re_rented_date) BETWEEN ? AND ?', [1 * 365, 5 * 365]);
                                break;
                            case '6m - 1y':
                                $q->orWhereRaw('DATEDIFF(CURDATE(), pmd.re_rented_date) BETWEEN ? AND ?', [6 * 30, 12 * 30]);
                                break;
                            case '1m - 6m':
                                $q->orWhereRaw('DATEDIFF(CURDATE(), pmd.re_rented_date) BETWEEN ? AND ?', [1 * 30, 6 * 30]);
                                break;
                            case 'm1-':
                                $q->orWhereRaw('DATEDIFF(CURDATE(), pmd.re_rented_date) < ?', [1 * 30]);
                                break;
                        }
                    }
                });
            })

            // Apply the special filtering logic for locality, block, and plot records
            ->where(function ($mainQuery) use ($filter) {
                // Case 1: Non-joint properties (is_joint_property = 0 or false)
                $mainQuery->where(function ($nonJointQuery) use ($filter) {
                    $nonJointQuery->where(function ($q) {
                        $q->whereNull('pm.is_joint_property')
                            ->orWhere('pm.is_joint_property', '')
                            ->orWhere('pm.is_joint_property', '0')
                            ->orWhere('pm.is_joint_property', '!=', '1');
                    });

                    // Apply locality filter if provided
                    if (!empty($filter['localityRecord'])) {
                        $nonJointQuery->where('pm.new_colony_name', $filter['localityRecord']);
                    }

                    // Apply block filter if provided
                    if (!empty($filter['blockRecord'])) {
                        $nonJointQuery->where('pm.block_no', $filter['blockRecord']);
                    }

                    // Apply plot filter if provided - only for non-joint properties
                    if (!empty($filter['plotRecord'])) {
                        // $nonJointQuery->where('pm.plot_or_property_no', 'like', $filter['plotRecord'] . '%');
                        $nonJointQuery->where('pm.plot_or_property_no', $filter['plotRecord']);
                    }
                });

                // Case 2: Joint properties (is_joint_property = 1 or true)
                $mainQuery->orWhere(function ($jointQuery) use ($filter) {
                    $jointQuery->where('pm.is_joint_property', '1');

                    // Apply locality filter if provided - from property_masters
                    if (!empty($filter['localityRecord'])) {
                        $jointQuery->where('pm.new_colony_name', $filter['localityRecord']);
                    }

                    // Apply block filter if provided - from property_masters
                    if (!empty($filter['blockRecord'])) {
                        $jointQuery->where('pm.block_no', $filter['blockRecord']);
                    }

                    // Apply plot filter if provided - from splited_property_details
                    if (!empty($filter['plotRecord'])) {
                        // $jointQuery->where('spd.plot_flat_no', 'like', $filter['plotRecord'] . '%');
                        $jointQuery->where('spd.plot_flat_no', $filter['plotRecord']);
                    }
                });
            });

        $queryWithNames = DB::table(DB::raw("({$query->toSql()}) as t"))
            ->mergeBindings($query)
            ->leftJoin('items as prop_groups', 't.land_type', '=', 'prop_groups.id')
            ->leftJoin('items as prop_status', 't.property_status', '=', 'prop_status.id')
            ->leftJoin('items as prop_type', 't.property_type', '=', 'prop_type.id')
            ->leftJoin('items as prop_sub_type', 't.property_sub_type', '=', 'prop_sub_type.id')
            ->leftJoin('items as lease_types', 't.type_of_lease', '=', 'lease_types.id')
            ->leftJoin('items as unit_types', 't.unit', '=', 'unit_types.id')
            ->select(
                't.id',
                't.old_propert_id',
                't.unique_propert_id',
                't.file_no',
                't.child_id',
                't.new_colony_name',
                't.area_in_sqm',
                't.gr',
                't.current_lessee_name',
                't.lease_tenure',
                't.address',
                't.land_value',
                't.total_dues',
                't.is_re_rented',
                't.re_rented_date',
                't.phone_no',
                't.gr_in_re_rs',
                'prop_groups.item_name as land_type',
                'prop_sub_type.item_name as land_sub_type',
                'prop_status.item_name as status',
                'prop_type.item_name as land_use',
                'lease_types.item_name as lease_type',
                't.colony',
                't.remarks',
                't.doe',
                't.input_area',
                'unit_types.item_name as unit'
            );

        $counterQuery = clone $query;
        $counter = $counterQuery->count();

        if ($withLimit && is_numeric($limit) && is_numeric($offset)) {
            $finalQuery = $queryWithNames->limit((int)$limit)->offset((int)$offset);
        } else {
            $finalQuery = clone($queryWithNames);
        }

        $rows = $finalQuery->get();

        if (!$withLimit) {
            return $rows;
        } else {
            return ['rows' => $rows, 'counter' => $counter];
        }
    }

    // public function colonyWiseFilterResults($filter = [], $withLimit = true)
    // {
    //     if ($withLimit) {
    //         $limit = isset($filter['limit']) ? $filter['limit'] : 50;
    //         $pageNum = isset($filter['page']) ? $filter['page'] : 1;
    //         $offset = ($pageNum - 1) * $limit;
    //     }

    //     $userSectionsIdList = $filter['section_id'];
    //     unset($filter['section_id']);

    //     /**lesse name filter */
    //     if (isset($filter['name'])) {
    //         $where[] = "lesse_name like '%" . $filter['name'] . "%'";
    //     }
    //     /**contact filter */
    //     if (isset($filter['contact'])) {
    //         $where[] = "phone_no like '" . $filter['contact'] . "%'";
    //     }

    //     /** property inspection details */ //outstandingDues
    //     if (isset($filter['outstandingDues'])) {
    //         $min = $filter['outstandingDues']['min'];
    //         $max = $filter['outstandingDues']['max'];
    //         if ($min != "" && $max != "") {
    //             $totalDues = "total_dues between $min and $max";
    //         } else {
    //             if ($min != "") {
    //                 $totalDues = "total_dues >= $min ";
    //             }
    //             if ($max != "") {
    //                 $totalDues = "total_dues <= $max ";
    //             }
    //         }
    //         $where[] = $totalDues;
    //     }

    //     $query = DB::table('property_masters as pm')
    //         ->leftJoin('splited_property_details as spd', 'pm.id', '=', 'spd.property_master_id')
    //         ->leftJoin(DB::raw('(select *,case when date_of_expiration is null then null else timestampdiff(year, doe, date_of_expiration ) end as lease_tenure from property_lease_details) as pld'), 'pm.id', '=', 'pld.property_master_id')
    //         ->leftJoin('old_colonies as oc', 'pm.new_colony_name', '=', 'oc.id')
    //         ->{isset($filter['reEnteredSince']) ? 'join' : 'leftJoin'}('property_misc_details as pmd', function ($join) {
    //             $join->on('pm.id', '=', 'pmd.property_master_id')
    //                 ->where(function ($query) {
    //                     $query->where(function ($q) {
    //                         $q->whereNull('pm.is_joint_property')
    //                             ->whereNull('pmd.splited_property_detail_id');
    //                     })->orWhere(function ($q) {
    //                         $q->whereNotNull('pm.is_joint_property')
    //                             ->whereColumn('pmd.splited_property_detail_id', '=', 'spd.id');
    //                     });
    //                 });
    //         })
    //         ->leftJoin(DB::raw('(select * from current_lessee_details where flat_id is null) as cld'), function ($join) {
    //             $join->on('pm.id', '=', 'cld.property_master_id')
    //                 ->where(function ($query) {
    //                     $query->where(function ($query) {
    //                         $query->whereNull('pm.is_joint_property')
    //                             ->whereNull('cld.splited_property_detail_id');
    //                     })->orWhere(function ($query) {
    //                         $query->whereNotNull('pm.is_joint_property')
    //                             ->whereColumn('spd.id', '=', 'cld.splited_property_detail_id');
    //                     });
    //                 });
    //         })
    //         ->leftJoin('property_inspection_demand_details as pid', function ($join) {
    //             $join->on('pm.id', '=', 'pid.property_master_id')
    //                 ->where(function ($query) {
    //                     $query->where(function ($query) {
    //                         $query->whereNull('pm.is_joint_property')
    //                             ->whereNull('pid.splited_property_detail_id');
    //                     })->orWhere(function ($query) {
    //                         $query->whereNotNull('pm.is_joint_property')
    //                             ->whereColumn('spd.id', '=', 'pid.splited_property_detail_id');
    //                     });
    //                 });
    //         })
    //         ->leftJoin('property_contact_details as pcd', function ($join) {
    //             $join->on('pm.id', '=', 'pcd.property_master_id')
    //                 ->where(function ($query) {
    //                     $query->where(function ($query) {
    //                         $query->whereNull('pm.is_joint_property')
    //                             ->whereNull('pcd.splited_property_detail_id');
    //                     })->orWhere(function ($query) {
    //                         $query->whereNotNull('pm.is_joint_property')
    //                             ->whereColumn('spd.id', '=', 'pcd.splited_property_detail_id');
    //                     });
    //                 });
    //         })
    //         ->select(
    //             'pm.id',
    //             'pm.old_propert_id',
    //             'pm.unique_propert_id',
    //             'pm.file_no',
    //             DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN "" ELSE spd.child_prop_id END AS child_id'),
    //             'pm.land_type',
    //             'pm.new_colony_name',
    //             'pm.block_no',
    //             'pm.plot_or_property_no',
    //             'pm.property_type',
    //             'pm.property_sub_type',
    //             DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pm.status ELSE spd.property_status END AS property_status'),
    //             DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pld.plot_area ELSE spd.current_area END AS input_area'),
    //             DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pld.unit ELSE spd.unit END AS unit'),
    //             DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pld.plot_area_in_sqm ELSE spd.area_in_sqm END AS area_in_sqm'),
    //             DB::raw("CASE WHEN pmd.is_gr_revised_ever = 1 THEN 'yes' ELSE 'No' END AS gr"),
    //             'cld.lessees_name as current_lessee_name',
    //             DB::raw('case when date_of_expiration is null then null else timestampdiff(year, doe, date_of_expiration ) end as lease_tenure'),
    //             'pld.doe',
    //             'pld.doa',
    //             'pld.date_of_expiration',
    //             'pld.type_of_lease',
    //             DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pld.plot_value_cr ELSE spd.plot_value_cr END AS land_value'),
    //             DB::raw('case when pm.is_joint_property is null then pld.presently_known_as else spd.presently_known_as end as address'),
    //             DB::raw('coalesce(pid.total_dues,0) as total_dues'),
    //             'pmd.is_re_rented',
    //             'pmd.re_rented_date',
    //             'pcd.phone_no',
    //             DB::raw('CASE 
    //             WHEN pm.is_joint_property IS NULL THEN COALESCE(pld.present_ground_rent, CAST(pld.gr_in_re_rs AS DECIMAL(12,2))) 
    //             ELSE spd.present_ground_rent 
    //             END AS gr_in_re_rs'),
    //             'oc.name as colony',
    //             'oc.id as colony_id',
    //             'pm.additional_remark as remarks'
    //         )
    //         ->when(isset($filter['land_type']), fn($q) => $q->where('pm.land_type', $filter['land_type']))

    //         ->when(
    //             !empty($filter['property_type']),
    //             fn($q) =>
    //             $q->whereIn('pm.property_type', $filter['property_type'])
    //         )

    //         ->when(
    //             !empty($filter['property_sub_type']),
    //             fn($q) =>
    //             $q->whereIn('pm.property_sub_type', $filter['property_sub_type'])
    //         )

    //         ->when(
    //             !empty($filter['land_status']),
    //             function ($q) use ($filter) {
    //                 $case = DB::raw("CASE WHEN pm.is_joint_property IS NULL THEN pm.status ELSE spd.property_status END");
    //                 return $q->whereIn($case, $filter['land_status']);
    //             }
    //         )

    //         ->when(
    //             !empty($filter['colony']),
    //             fn($q) =>
    //             $q->whereIn('new_colony_name', $filter['colony'])
    //         )

    //         // Apply all other common filters first
    //         ->when(
    //             !empty($userSectionsIdList),
    //             fn($q) =>
    //             $q->whereIn('section_code', $userSectionsIdList)
    //         )

    //         // ->when(
    //         //     !empty($userSectionsIdList) && $this->getCurrentUserRole() !== 'it-cell',
    //         //     fn($q) =>
    //         //     $q->whereIn('section_code', $userSectionsIdList)
    //         // )

    //         ->when(
    //             !empty($filter['propertyId']),
    //             fn($q) =>
    //             $q->where('old_propert_id', 'like', $filter['propertyId'] . '%')
    //         )

    //         ->when(!empty($filter['land_size']), function ($q) use ($filter) {
    //             $min = (float) $filter['land_size']['min'];
    //             $max = (float) $filter['land_size']['max'];

    //             $case = DB::raw("CAST(
    //             CASE
    //                 WHEN pm.is_joint_property IS NULL THEN pld.plot_area_in_sqm
    //                 ELSE spd.area_in_sqm
    //             END AS DECIMAL(10,2))");
    //             if ($min > 0 && $max > 0) {
    //                 $q->whereBetween($case, [$min, $max]);
    //             } elseif ($min > 0) {
    //                 $q->where($case, '>=', $min);
    //             } elseif ($max > 0) {
    //                 $q->where($case, '<=', $max);
    //             }
    //         })

    //         ->when(!empty($filter['date_of_execution']), function ($q) use ($filter) {
    //             $min = $filter['date_of_execution']['min'] ?? "";
    //             $max = $filter['date_of_execution']['max'] ?? "";
    //             if ($min !== "" && $max !== "") {
    //                 $q->whereBetween('doe', [$min, $max]);
    //             } elseif ($min !== "") {
    //                 $q->where('doe', '>=', $min);
    //             } elseif ($max !== "") {
    //                 $q->where('doe', '<=', $max);
    //             }
    //         })
    //         ->when(!empty($filter['date_of_allottment']), function ($q) use ($filter) {
    //             $min = $filter['date_of_allottment']['min'] ?? "";
    //             $max = $filter['date_of_allottment']['max'] ?? "";

    //             if ($min !== "" && $max !== "") {
    //                 $q->whereBetween('doa', [$min, $max]);
    //             } elseif ($min !== "") {
    //                 $q->where('doa', '>=', $min);
    //             } elseif ($max !== "") {
    //                 $q->where('doa', '<=', $max);
    //             }
    //         })
    //         ->when(!empty($filter['date_of_expiration']), function ($q) use ($filter) {
    //             $min = $filter['date_of_expiration']['min'] ?? "";
    //             $max = $filter['date_of_expiration']['max'] ?? "";

    //             if ($min !== "" && $max !== "") {
    //                 $q->whereBetween('date_of_expiration', [$min, $max]);
    //             } elseif ($min !== "") {
    //                 $q->where('date_of_expiration', '>=', $min);
    //             } elseif ($max !== "") {
    //                 $q->where('date_of_expiration', '<=', $max);
    //             }
    //         })
    //         ->when(!empty($filter['land_value']), function ($q) use ($filter) {
    //             $min = (float) $filter['land_value']['min'];
    //             $max = (float) $filter['land_value']['max'];

    //             $case = DB::raw("CAST(
    //             CASE
    //                 WHEN pm.is_joint_property IS NULL THEN pld.plot_value_cr
    //                 ELSE spd.plot_value_cr
    //             END AS DECIMAL(10,2))");
    //             if ($min > 0 && $max > 0) {
    //                 $q->whereBetween($case, [$min, $max]);
    //             } elseif ($min > 0) {
    //                 $q->where($case, '>=', $min);
    //             } elseif ($max > 0) {
    //                 $q->where($case, '<=', $max);
    //             }
    //         })

    //         ->when(
    //             !empty($filter['leaseDeed']),
    //             fn($q) =>
    //             $q->whereIn('type_of_lease', $filter['leaseDeed'])
    //         )

    //         ->when(
    //             !empty($filter['propertyAddress']),
    //             fn($q) =>
    //             $q->where('address', 'like', '%' . $filter['propertyAddress'] . '%')
    //         )

    //         ->when(!empty($filter['groundRent']), function ($q) use ($filter) {
    //             $min = $filter['groundRent']['min'] ?? "";
    //             $max = $filter['groundRent']['max'] ?? "";

    //             if ($min !== "" && $max !== "") {
    //                 $q->whereBetween('gr_in_re_rs', [$min, $max]);
    //             } elseif ($min !== "") {
    //                 $q->where('gr_in_re_rs', '>=', $min);
    //             } elseif ($max !== "") {
    //                 $q->where('gr_in_re_rs', '<=', $max);
    //             }
    //         })
    //         ->when(
    //             !empty($filter['name']),
    //             fn($q) =>
    //             $q->where('cld.lessees_name', 'like', '%' . $filter['name'] . '%')
    //         )

    //         ->when(
    //             !empty($filter['contact']),
    //             fn($q) =>
    //             $q->where('phone_no', 'like', $filter['contact'] . '%')
    //         )

    //         ->when(!empty($filter['outstandingDues']), function ($q) use ($filter) {
    //             $min = (float) $filter['outstandingDues']['min'];
    //             $max = (float) $filter['outstandingDues']['max'];

    //             $condition = DB::raw("CAST(pid.total_dues AS DECIMAL(10,2))");
    //             if ($min > 0 && $max > 0) {
    //                 $q->whereBetween($condition, [$min, $max]);
    //             } elseif ($min > 0) {
    //                 $q->where($condition, '>=', $min);
    //             } elseif ($max > 0) {
    //                 $q->where($condition, '<=', $max);
    //             }
    //         })
    //         ->when(isset($filter['leaseTenure']), function ($query) use ($filter) {
    //             $query->where(function ($q) use ($filter) {
    //                 foreach ($filter['leaseTenure'] as $range) {
    //                     switch ($range) {
    //                         case 'Perpetual':
    //                             $q->orWhereNull('pld.lease_tenure');
    //                             break;
    //                         case '0 - 5':
    //                             $q->orWhereBetween('pld.lease_tenure', [0, 5]);
    //                             break;
    //                         case '5 - 25':
    //                             $q->orWhereBetween('pld.lease_tenure', [5, 25]);
    //                             break;
    //                         case '25 - 50':
    //                             $q->orWhereBetween('pld.lease_tenure', [25, 50]);
    //                             break;
    //                         case '50 - 75':
    //                             $q->orWhereBetween('pld.lease_tenure', [50, 75]);
    //                             break;
    //                         case '75+':
    //                             $q->orWhere('pld.lease_tenure', '>', 75);
    //                             break;
    //                     }
    //                 }
    //             });
    //         })
    //         ->when(isset($filter['reEnteredSince']), function ($query) use ($filter) {
    //             $query->where(function ($q) use ($filter) {
    //                 foreach ($filter['reEnteredSince'] as $range) {
    //                     switch ($range) {
    //                         case 'y5+':
    //                             $q->orWhereRaw('DATEDIFF(CURDATE(), pmd.re_rented_date) > ?', [5 * 365]);
    //                             break;
    //                         case '1y - 5y':
    //                             $q->orWhereRaw('DATEDIFF(CURDATE(), pmd.re_rented_date) BETWEEN ? AND ?', [1 * 365, 5 * 365]);
    //                             break;
    //                         case '6m - 1y':
    //                             $q->orWhereRaw('DATEDIFF(CURDATE(), pmd.re_rented_date) BETWEEN ? AND ?', [6 * 30, 12 * 30]);
    //                             break;
    //                         case '1m - 6m':
    //                             $q->orWhereRaw('DATEDIFF(CURDATE(), pmd.re_rented_date) BETWEEN ? AND ?', [1 * 30, 6 * 30]);
    //                             break;
    //                         case 'm1-':
    //                             $q->orWhereRaw('DATEDIFF(CURDATE(), pmd.re_rented_date) < ?', [1 * 30]);
    //                             break;
    //                     }
    //                 }
    //             });
    //         })

    //         // Apply the special filtering logic for locality, block, and plot records
    //         ->where(function ($mainQuery) use ($filter) {
    //             // Case 1: Non-joint properties (is_joint_property = 0 or false)
    //             $mainQuery->where(function ($nonJointQuery) use ($filter) {
    //                 $nonJointQuery->where(function ($q) {
    //                     $q->whereNull('pm.is_joint_property')
    //                         ->orWhere('pm.is_joint_property', '')
    //                         ->orWhere('pm.is_joint_property', '0')
    //                         ->orWhere('pm.is_joint_property', '!=', '1');
    //                 });

    //                 // Apply locality filter if provided
    //                 if (!empty($filter['localityRecord'])) {
    //                     $nonJointQuery->where('pm.new_colony_name', $filter['localityRecord']);
    //                 }

    //                 // Apply block filter if provided
    //                 if (!empty($filter['blockRecord'])) {
    //                     $nonJointQuery->where('pm.block_no', $filter['blockRecord']);
    //                 }

    //                 // Apply plot filter if provided - only for non-joint properties
    //                 if (!empty($filter['plotRecord'])) {
    //                     $nonJointQuery->where('pm.plot_or_property_no', 'like', $filter['plotRecord'] . '%');
    //                 }
    //             });

    //             // Case 2: Joint properties (is_joint_property = 1 or true)
    //             $mainQuery->orWhere(function ($jointQuery) use ($filter) {
    //                 $jointQuery->where('pm.is_joint_property', '1');

    //                 // Apply locality filter if provided - from property_masters
    //                 if (!empty($filter['localityRecord'])) {
    //                     $jointQuery->where('pm.new_colony_name', $filter['localityRecord']);
    //                 }

    //                 // Apply block filter if provided - from property_masters
    //                 if (!empty($filter['blockRecord'])) {
    //                     $jointQuery->where('pm.block_no', $filter['blockRecord']);
    //                 }

    //                 // Apply plot filter if provided - from splited_property_details
    //                 if (!empty($filter['plotRecord'])) {
    //                     $jointQuery->where('spd.plot_flat_no', 'like', $filter['plotRecord'] . '%');
    //                 }
    //             });
    //         });

    //     $queryWithNames = DB::table(DB::raw("({$query->toSql()}) as t"))
    //         ->mergeBindings($query)
    //         ->leftJoin('items as prop_groups', 't.land_type', '=', 'prop_groups.id')
    //         ->leftJoin('items as prop_status', 't.property_status', '=', 'prop_status.id')
    //         ->leftJoin('items as prop_type', 't.property_type', '=', 'prop_type.id')
    //         ->leftJoin('items as prop_sub_type', 't.property_sub_type', '=', 'prop_sub_type.id')
    //         ->leftJoin('items as lease_types', 't.type_of_lease', '=', 'lease_types.id')
    //         ->leftJoin('items as unit_types', 't.unit', '=', 'unit_types.id')
    //         ->select(
    //             't.id',
    //             't.old_propert_id',
    //             't.unique_propert_id',
    //             't.file_no',
    //             't.child_id',
    //             't.new_colony_name',
    //             't.area_in_sqm',
    //             't.gr',
    //             't.current_lessee_name',
    //             't.lease_tenure',
    //             't.address',
    //             't.land_value',
    //             't.total_dues',
    //             't.is_re_rented',
    //             't.re_rented_date',
    //             't.phone_no',
    //             't.gr_in_re_rs',
    //             'prop_groups.item_name as land_type',
    //             'prop_sub_type.item_name as land_sub_type',
    //             'prop_status.item_name as status',
    //             'prop_type.item_name as land_use',
    //             'lease_types.item_name as lease_type',
    //             't.colony',
    //             't.remarks',
    //             't.doe',
    //             't.input_area',
    //             'unit_types.item_name as unit'
    //         );

    //     $counterQuery = clone $query;
    //     $counter = $counterQuery->count();

    //     if ($withLimit && is_numeric($limit) && is_numeric($offset)) {
    //         $finalQuery = $queryWithNames->limit((int)$limit)->offset((int)$offset);
    //     } else {
    //         $finalQuery = clone($queryWithNames);
    //     }

    //     $rows = $finalQuery->get();

    //     if (!$withLimit) {
    //         return $rows;
    //     } else {
    //         return ['rows' => $rows, 'counter' => $counter];
    //     }
    // }

    // public function colonyWiseFilterResults($filter = [], $withLimit =  true)
    // {
    //     if ($withLimit) {
    //         $limit  = isset($filter['limit']) ? $filter['limit'] : 50;
    //         $pageNum = isset($filter['page']) ? $filter['page'] : 1;
    //         $offset = ($pageNum - 1) * $limit;
    //         //$countRows = "SQL_CALC_FOUND_ROWS";
    //     }

    //     /** For section users the user should be able to get report of properities in their section only
    //      *  for a user add additional filter of colony property tye and subtype
    //      */
    //     // $userSectionsIdList = Auth::check() ? Auth::user()->sections->pluck('id')->toArray() : [];
    //     $userSectionsIdList = $filter['section_id'];
    //     // dd($userSectionsIdList);
    //     unset($filter['section_id']);


    //     /**lesse name filter */
    //     if (isset($filter['name'])) {
    //         $where[] = "lesse_name like '%" . $filter['name'] . "%'";
    //     }
    //     /**contact filter */
    //     if (isset($filter['contact'])) {
    //         $where[] = "phone_no like '" . $filter['contact'] . "%'";
    //     }

    //     /** property inspection details */ //outstandingDues
    //     if (isset($filter['outstandingDues'])) {
    //         $min = $filter['outstandingDues']['min'];
    //         $max = $filter['outstandingDues']['max'];
    //         if ($min != "" && $max != "") {
    //             $totalDues = "total_dues between $min and $max";
    //         } else {
    //             if ($min != "") {
    //                 $totalDues = "total_dues >= $min ";
    //             }
    //             if ($max != "") {
    //                 $totalDues =  "total_dues <= $max ";
    //             }
    //         }
    //         $where[] = $totalDues;
    //     }

    //     $query = DB::table('property_masters as pm')
    //         ->leftJoin('splited_property_details as spd', 'pm.id', '=', 'spd.property_master_id')
    //         ->leftJoin(DB::raw('(select *,case when date_of_expiration is null then null else timestampdiff(year, doe, date_of_expiration ) end as lease_tenure from property_lease_details) as pld'), 'pm.id', '=', 'pld.property_master_id')
    //         ->leftJoin('old_colonies as oc', 'pm.new_colony_name', '=', 'oc.id')
    //         ->{isset($filter['reEnteredSince']) ? 'join' : 'leftJoin'}('property_misc_details as pmd', function ($join) {
    //             $join->on('pm.id', '=', 'pmd.property_master_id')
    //                 ->where(function ($query) {
    //                     $query->where(function ($q) {
    //                         $q->whereNull('pm.is_joint_property')
    //                             ->whereNull('pmd.splited_property_detail_id');
    //                     })->orWhere(function ($q) {
    //                         $q->whereNotNull('pm.is_joint_property')
    //                             ->whereColumn('pmd.splited_property_detail_id', '=', 'spd.id');
    //                     });
    //                 });
    //         })
    //         /* ->when(count($userSectionsIdList) > 0, function ($quer) use ($userSectionsIdList) {
    //             return $quer->join('property_section_mappings as psm', function ($join) use ($userSectionsIdList) {
    //                 $join->on('pm.new_colony_name', '=', 'psm.colony_id');
    //                 $join->whereColumn('pm.property_type', 'psm.property_type');
    //                 $join->whereColumn('pm.property_sub_type', 'psm.property_subtype');
    //                 $join->whereIn('psm.section_id', $userSectionsIdList);
    //             });
    //         }) */
    //         ->leftJoin(DB::raw('(select * from current_lessee_details where flat_id is null) as cld'), function ($join) {
    //             $join->on('pm.id', '=', 'cld.property_master_id')
    //                 ->where(function ($query) {
    //                     $query->where(function ($query) {
    //                         $query->whereNull('pm.is_joint_property')
    //                             ->whereNull('cld.splited_property_detail_id');
    //                     })->orWhere(function ($query) {
    //                         $query->whereNotNull('pm.is_joint_property')
    //                             ->whereColumn('spd.id', '=', 'cld.splited_property_detail_id');
    //                     });
    //                 });
    //         })
    //         /* ->leftJoin(DB::raw("
    //             (
    //                 SELECT 
    //                     property_master_id, 
    //                     splited_property_detail_id, 
    //                     GROUP_CONCAT(lessee_name SEPARATOR ', ') AS lessee_name
    //                 FROM property_transferred_lessee_details
    //                 WHERE flat_id IS NULL AND process_of_transfer = 'Original'
    //                 GROUP BY property_master_id, splited_property_detail_id
    //             ) AS ptld
    //         "), function ($join) {
    //             $join->on('pm.id', '=', 'ptld.property_master_id')
    //                 ->where(function ($query) {
    //                     $query->where(function ($query) {
    //                         $query->whereNull('pm.is_joint_property')
    //                             ->whereNull('ptld.splited_property_detail_id');
    //                     })->orWhere(function ($query) {
    //                         $query->whereNotNull('pm.is_joint_property')
    //                             ->whereColumn('spd.id', '=', 'ptld.splited_property_detail_id');
    //                     });
    //                 });
    //         }) */
    //         ->leftJoin('property_inspection_demand_details as pid', function ($join) {
    //             $join->on('pm.id', '=', 'pid.property_master_id')
    //                 ->where(function ($query) {
    //                     $query->where(function ($query) {
    //                         $query->whereNull('pm.is_joint_property')
    //                             ->whereNull('pid.splited_property_detail_id');
    //                     })->orWhere(function ($query) {
    //                         $query->whereNotNull('pm.is_joint_property')
    //                             ->whereColumn('spd.id', '=', 'pid.splited_property_detail_id');
    //                     });
    //                 });
    //         })
    //         ->leftJoin('property_contact_details as pcd', function ($join) {
    //             $join->on('pm.id', '=', 'pcd.property_master_id')
    //                 ->where(function ($query) {
    //                     $query->where(function ($query) {
    //                         $query->whereNull('pm.is_joint_property')
    //                             ->whereNull('pcd.splited_property_detail_id');
    //                     })->orWhere(function ($query) {
    //                         $query->whereNotNull('pm.is_joint_property')
    //                             ->whereColumn('spd.id', '=', 'pcd.splited_property_detail_id');
    //                     });
    //                 });
    //         })
    //         ->select(
    //             'pm.id',
    //             'pm.old_propert_id',
    //             'pm.unique_propert_id',
    //             'pm.file_no',
    //             DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN "" ELSE spd.child_prop_id END AS child_id'),
    //             'pm.land_type',
    //             'pm.new_colony_name',
    //             'pm.block_no',
    //             'pm.plot_or_property_no',
    //             'pm.property_type',
    //             'pm.property_sub_type',
    //             DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pm.status ELSE spd.property_status END AS property_status'),
    //             DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pld.plot_area ELSE spd.current_area END AS input_area'),
    //             DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pld.unit ELSE spd.unit END AS unit'),
    //             DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pld.plot_area_in_sqm ELSE spd.area_in_sqm END AS area_in_sqm'),
    //             DB::raw("CASE WHEN pmd.is_gr_revised_ever = 1 THEN 'yes' ELSE 'No' END AS gr"),
    //             'cld.lessees_name as current_lessee_name',
    //             // 'ptld.lessee_name as original_lessee_name',
    //             DB::raw('case when date_of_expiration is null then null else timestampdiff(year, doe, date_of_expiration ) end as lease_tenure'),
    //             'pld.doe',
    //             'pld.doa',
    //             'pld.date_of_expiration',
    //             'pld.type_of_lease',
    //             DB::raw('CASE WHEN pm.is_joint_property IS NULL THEN pld.plot_value_cr ELSE spd.plot_value_cr END AS land_value'),
    //             DB::raw('case when pm.is_joint_property is null then pld.presently_known_as else spd.presently_known_as end as address'),
    //             DB::raw('coalesce(pid.total_dues,0) as total_dues'),
    //             'pmd.is_re_rented',
    //             'pmd.re_rented_date',
    //             'pcd.phone_no',
    //             DB::raw('CASE 
    //                 WHEN pm.is_joint_property IS NULL THEN COALESCE(pld.present_ground_rent, CAST(pld.gr_in_re_rs AS DECIMAL(12,2))) 
    //                 ELSE spd.present_ground_rent 
    //                 END AS gr_in_re_rs'),
    //             'oc.name as colony',
    //             'oc.id as colony_id',
    //             'pm.additional_remark as remarks'
    //         )
    //         ->when(isset($filter['land_type']), fn($q) => $q->where('pm.land_type', $filter['land_type']))

    //         ->when(
    //             !empty($filter['property_type']),
    //             fn($q) =>
    //             $q->whereIn('pm.property_type', $filter['property_type'])
    //         )

    //         ->when(
    //             !empty($filter['property_sub_type']),
    //             fn($q) =>
    //             $q->whereIn('pm.property_sub_type', $filter['property_sub_type'])
    //         )

    //         ->when(
    //             !empty($filter['land_status']),
    //             function ($q) use ($filter) {
    //                 $case = DB::raw("CASE WHEN pm.is_joint_property IS NULL THEN pm.status ELSE spd.property_status END");
    //                 return $q->whereIn($case, $filter['land_status']);
    //             }
    //         )

    //         ->when(
    //             !empty($filter['colony']),
    //             fn($q) =>
    //             $q->whereIn('new_colony_name', $filter['colony'])
    //         )
    //         ->when(
    //             !empty($filter['localityRecord']),
    //             fn($q) =>
    //             $q->where('new_colony_name', $filter['localityRecord'])
    //         )
    //         ->when(
    //             !empty($filter['blockRecord']),
    //             fn($q) =>
    //             $q->where('block_no', $filter['blockRecord'])
    //         )
    //         ->when(
    //             !empty($filter['plotRecord']),
    //             fn($q) =>
    //             $q->where('plot_or_property_no', $filter['plotRecord'])
    //         )
    //         ->when(
    //             !empty($userSectionsIdList),
    //             fn($q) =>
    //             $q->whereIn('section_code', $userSectionsIdList)
    //         )

    //         ->when(
    //             !empty($filter['propertyId']),
    //             fn($q) =>
    //             $q->where('old_propert_id', 'like', $filter['propertyId'] . '%')
    //         )

    //         ->when(!empty($filter['land_size']), function ($q) use ($filter) {
    //             $min = (float) $filter['land_size']['min'];
    //             $max = (float) $filter['land_size']['max'];

    //             $case = DB::raw("CAST(
    //                 CASE
    //                     WHEN pm.is_joint_property IS NULL THEN pld.plot_area_in_sqm
    //                     ELSE spd.area_in_sqm
    //                 END AS DECIMAL(10,2))");
    //             if ($min > 0 && $max > 0) {
    //                 $q->whereBetween($case, [$min, $max]);
    //             } elseif ($min > 0) {
    //                 $q->where($case, '>=', $min);
    //             } elseif ($max > 0) {
    //                 $q->where($case, '<=', $max);
    //             }
    //         })

    //         ->when(!empty($filter['date_of_execution']), function ($q) use ($filter) {
    //             $min = $filter['date_of_execution']['min'] ?? "";
    //             $max = $filter['date_of_execution']['max'] ?? "";
    //             if ($min !== "" && $max !== "") {
    //                 $q->whereBetween('doe', [$min, $max]);
    //             } elseif ($min !== "") {
    //                 $q->where('doe', '>=', $min);
    //             } elseif ($max !== "") {
    //                 $q->where('doe', '<=', $max);
    //             }
    //         })
    //         ->when(!empty($filter['date_of_allottment']), function ($q) use ($filter) {
    //             $min = $filter['date_of_allottment']['min'] ?? "";
    //             $max = $filter['date_of_allottment']['max'] ?? "";

    //             if ($min !== "" && $max !== "") {
    //                 $q->whereBetween('doa', [$min, $max]);
    //             } elseif ($min !== "") {
    //                 $q->where('doa', '>=', $min);
    //             } elseif ($max !== "") {
    //                 $q->where('doa', '<=', $max);
    //             }
    //         })
    //         ->when(!empty($filter['date_of_expiration']), function ($q) use ($filter) {
    //             $min = $filter['date_of_expiration']['min'] ?? "";
    //             $max = $filter['date_of_expiration']['max'] ?? "";

    //             if ($min !== "" && $max !== "") {
    //                 $q->whereBetween('date_of_expiration', [$min, $max]);
    //             } elseif ($min !== "") {
    //                 $q->where('date_of_expiration', '>=', $min);
    //             } elseif ($max !== "") {
    //                 $q->where('date_of_expiration', '<=', $max);
    //             }
    //         })
    //         ->when(!empty($filter['land_value']), function ($q) use ($filter) {
    //             $min = (float) $filter['land_value']['min'];
    //             $max = (float) $filter['land_value']['max'];

    //             $case = DB::raw("CAST(
    //                 CASE
    //                     WHEN pm.is_joint_property IS NULL THEN pld.plot_value_cr
    //                     ELSE spd.plot_value_cr
    //                 END AS DECIMAL(10,2))");
    //             if ($min > 0 && $max > 0) {
    //                 $q->whereBetween($case, [$min, $max]);
    //             } elseif ($min > 0) {
    //                 $q->where($case, '>=', $min);
    //             } elseif ($max > 0) {
    //                 $q->where($case, '<=', $max);
    //             }
    //         })

    //         ->when(
    //             !empty($filter['leaseDeed']),
    //             fn($q) =>
    //             $q->whereIn('type_of_lease', $filter['leaseDeed'])
    //         )

    //         ->when(
    //             !empty($filter['propertyAddress']),
    //             fn($q) =>
    //             $q->where('address', 'like', '%' . $filter['propertyAddress'] . '%')
    //         )

    //         ->when(!empty($filter['groundRent']), function ($q) use ($filter) {
    //             $min = $filter['groundRent']['min'] ?? "";
    //             $max = $filter['groundRent']['max'] ?? "";

    //             if ($min !== "" && $max !== "") {
    //                 $q->whereBetween('gr_in_re_rs', [$min, $max]);
    //             } elseif ($min !== "") {
    //                 $q->where('gr_in_re_rs', '>=', $min);
    //             } elseif ($max !== "") {
    //                 $q->where('gr_in_re_rs', '<=', $max);
    //             }
    //         })
    //         ->when(
    //             !empty($filter['name']),
    //             fn($q) =>
    //             $q->where('cld.lessees_name', 'like', '%' . $filter['name'] . '%')
    //         )

    //         ->when(
    //             !empty($filter['contact']),
    //             fn($q) =>
    //             $q->where('phone_no', 'like', $filter['contact'] . '%')
    //         )

    //         ->when(!empty($filter['outstandingDues']), function ($q) use ($filter) {
    //             $min = (float) $filter['outstandingDues']['min'];
    //             $max = (float) $filter['outstandingDues']['max'];

    //             $condition = DB::raw("CAST(pid.total_dues AS DECIMAL(10,2))");
    //             if ($min > 0 && $max > 0) {
    //                 $q->whereBetween($condition, [$min, $max]);
    //             } elseif ($min > 0) {
    //                 $q->where($condition, '>=', $min);
    //             } elseif ($max > 0) {
    //                 $q->where($condition, '<=', $max);
    //             }
    //         })
    //         ->when(isset($filter['leaseTenure']), function ($query) use ($filter) {
    //             $query->where(function ($q) use ($filter) {
    //                 foreach ($filter['leaseTenure'] as $range) {
    //                     switch ($range) {
    //                         case 'Perpetual':
    //                             $q->orWhereNull('pld.lease_tenure');
    //                             break;
    //                         case '0 - 5':
    //                             $q->orWhereBetween('pld.lease_tenure', [0, 5]);
    //                             break;
    //                         case '5 - 25':
    //                             $q->orWhereBetween('pld.lease_tenure', [5, 25]);
    //                             break;
    //                         case '25 - 50':
    //                             $q->orWhereBetween('pld.lease_tenure', [25, 50]);
    //                             break;
    //                         case '50 - 75':
    //                             $q->orWhereBetween('pld.lease_tenure', [50, 75]);
    //                             break;
    //                         case '75+':
    //                             $q->orWhere('pld.lease_tenure', '>', 75);
    //                             break;
    //                     }
    //                 }
    //             });
    //         })
    //         ->when(isset($filter['reEnteredSince']), function ($query) use ($filter) {
    //             $query->where(function ($q) use ($filter) {
    //                 foreach ($filter['reEnteredSince'] as $range) {
    //                     switch ($range) {
    //                         case 'y5+':
    //                             $q->orWhereRaw('DATEDIFF(CURDATE(), pmd.re_rented_date) > ?', [5 * 365]);
    //                             break;
    //                         case '1y - 5y':
    //                             $q->orWhereRaw('DATEDIFF(CURDATE(), pmd.re_rented_date) BETWEEN ? AND ?', [1 * 365, 5 * 365]);
    //                             break;
    //                         case '6m - 1y':
    //                             $q->orWhereRaw('DATEDIFF(CURDATE(), pmd.re_rented_date) BETWEEN ? AND ?', [6 * 30, 12 * 30]);
    //                             break;
    //                         case '1m - 6m':
    //                             $q->orWhereRaw('DATEDIFF(CURDATE(), pmd.re_rented_date) BETWEEN ? AND ?', [1 * 30, 6 * 30]);
    //                             break;
    //                         case 'm1-':
    //                             $q->orWhereRaw('DATEDIFF(CURDATE(), pmd.re_rented_date) < ?', [1 * 30]);
    //                             break;
    //                     }
    //                 }
    //             });
    //         });
    //     // dd($query->toSql(), $query->getBindings());
    //     $queryWithNames = DB::table(DB::raw("({$query->toSql()}) as t"))
    //         ->mergeBindings($query) // Required to merge bindings from the subquery
    //         ->leftJoin('items as prop_groups', 't.land_type', '=', 'prop_groups.id')
    //         ->leftJoin('items as prop_status', 't.property_status', '=', 'prop_status.id')
    //         ->leftJoin('items as prop_type', 't.property_type', '=', 'prop_type.id')
    //         ->leftJoin('items as prop_sub_type', 't.property_sub_type', '=', 'prop_sub_type.id')
    //         ->leftJoin('items as lease_types', 't.type_of_lease', '=', 'lease_types.id')
    //         ->leftJoin('items as unit_types', 't.unit', '=', 'unit_types.id')
    //         //land rates
    //         /*  ->leftJoin(DB::raw("
    //             (
    //                 SELECT 47 AS type, crlr.*
    //                 FROM circle_residential_land_rates crlr
    //                 WHERE 
    //                     (crlr.date_to IS NULL AND crlr.date_from <= CURRENT_DATE())
    //                     OR (crlr.date_from IS NULL AND crlr.date_to >= CURRENT_DATE())
    //                     OR (crlr.date_from <= CURRENT_DATE() AND crlr.date_to >= CURRENT_DATE())

    //                 UNION ALL

    //                 SELECT 48 AS type, cclr.*
    //                 FROM circle_commercial_land_rates cclr
    //                 WHERE 
    //                     (cclr.date_to IS NULL AND cclr.date_from <= CURRENT_DATE())
    //                     OR (cclr.date_from IS NULL AND cclr.date_to >= CURRENT_DATE())
    //                     OR (cclr.date_from <= CURRENT_DATE() AND cclr.date_to >= CURRENT_DATE())

    //                 UNION ALL

    //                 SELECT 49 AS type, cilr.*
    //                 FROM circle_institutional_land_rates cilr
    //                 WHERE 
    //                     (cilr.date_to IS NULL AND cilr.date_from <= CURRENT_DATE())
    //                     OR (cilr.date_from IS NULL AND cilr.date_to >= CURRENT_DATE())
    //                     OR (cilr.date_from <= CURRENT_DATE() AND cilr.date_to >= CURRENT_DATE())

    //                 UNION ALL

    //                 SELECT 469 AS type, cinlr.*
    //                 FROM circle_institutional_land_rates cinlr
    //                 WHERE 
    //                     (cinlr.date_to IS NULL AND cinlr.date_from <= CURRENT_DATE())
    //                     OR (cinlr.date_from IS NULL AND cinlr.date_to >= CURRENT_DATE())
    //                     OR (cinlr.date_from <= CURRENT_DATE() AND cinlr.date_to >= CURRENT_DATE())
    //             ) AS circlerates
    //         "), function ($join) {
    //             $join->on('t.colony_id', '=', 'circlerates.colony_id')
    //                 ->on('t.property_type', '=', 'circlerates.type');
    //         })
    //         ->leftJoin(DB::raw("
    //             (
    //                 SELECT 47 AS type, lrlr.*
    //                 FROM lndo_residential_land_rates lrlr
    //                 WHERE 
    //                     (lrlr.date_to IS NULL AND lrlr.date_from <= CURRENT_DATE())
    //                     OR (lrlr.date_from IS NULL AND lrlr.date_to >= CURRENT_DATE())
    //                     OR (lrlr.date_from <= CURRENT_DATE() AND lrlr.date_to >= CURRENT_DATE())

    //                 UNION ALL

    //                 SELECT 48 AS type, lclr.*
    //                 FROM lndo_commercial_land_rates lclr
    //                 WHERE 
    //                     (lclr.date_to IS NULL AND lclr.date_from <= CURRENT_DATE())
    //                     OR (lclr.date_from IS NULL AND lclr.date_to >= CURRENT_DATE())
    //                     OR (lclr.date_from <= CURRENT_DATE() AND lclr.date_to >= CURRENT_DATE())

    //                 UNION ALL

    //                 SELECT 49 AS type, lilr.*
    //                 FROM lndo_institutional_land_rates lilr
    //                 WHERE 
    //                     (lilr.date_to IS NULL AND lilr.date_from <= CURRENT_DATE())
    //                     OR (lilr.date_from IS NULL AND lilr.date_to >= CURRENT_DATE())
    //                     OR (lilr.date_from <= CURRENT_DATE() AND lilr.date_to >= CURRENT_DATE())

    //                 UNION ALL

    //                 SELECT 469 AS type, linlr.*
    //                 FROM lndo_institutional_land_rates linlr
    //                 WHERE 
    //                     (linlr.date_to IS NULL AND linlr.date_from <= CURRENT_DATE())
    //                     OR (linlr.date_from IS NULL AND linlr.date_to >= CURRENT_DATE())
    //                     OR (linlr.date_from <= CURRENT_DATE() AND linlr.date_to >= CURRENT_DATE())
    //             ) AS lndorates
    //         "), function ($join) {
    //             $join->on('t.colony_id', '=', 'lndorates.colony_id')
    //                 ->on('t.property_type', '=', 'lndorates.type');
    //         }) */
    //         ->select(
    //             't.id',
    //             't.old_propert_id',
    //             't.unique_propert_id',
    //             't.file_no',
    //             't.child_id',
    //             't.new_colony_name',
    //             't.area_in_sqm',
    //             't.gr',
    //             't.current_lessee_name',
    //             // 't.original_lessee_name',
    //             't.lease_tenure',
    //             't.address',
    //             't.land_value',
    //             't.total_dues',
    //             't.is_re_rented',
    //             't.re_rented_date',
    //             't.phone_no',
    //             't.gr_in_re_rs',
    //             'prop_groups.item_name as land_type',
    //             'prop_sub_type.item_name as land_sub_type',
    //             'prop_status.item_name as status',
    //             'prop_type.item_name as land_use',
    //             'lease_types.item_name as lease_type',
    //             't.colony',
    //             't.remarks',
    //             't.doe',
    //             /* 'circlerates.land_rate as circle_land_rate',
    //             'lndorates.land_rate as lndo_land_rate', */
    //             't.input_area',
    //             'unit_types.item_name as unit'
    //         );
    //     $counterQuery = clone $query;
    //     $counter = $counterQuery->count();
    //     /* $query = $queryWithNames->when($withLimit == true, function ($quer) use ($limit, $offset) {
    //         return $quer->limit($limit)->offset($offset);
    //     }); */
    //     if ($withLimit && is_numeric($limit) && is_numeric($offset)) {
    //         $finalQuery = $queryWithNames->limit((int)$limit)->offset((int)$offset);
    //     } else {
    //         $finalQuery = clone ($queryWithNames);
    //     }
    //     // dd($finalQuery->toSql(), $finalQuery->getBindings());
    //     $rows = $finalQuery->get();
    //     if (!$withLimit) {
    //         return $rows;
    //     } else {
    //         return ['rows' => $rows, 'counter' => $counter, /* 'query' => $query, 'filter' => $filter */];
    //     }
    // }
}
