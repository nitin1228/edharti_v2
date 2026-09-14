<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionReport extends Model
{
    protected $fillable = [

        'inspection_id',
        'inspection_officer',
        'senior_officer',
        'inspection_date',
        'book_number',
        'page_number',
        'construction_tallies',
        'sanction_plan_ref_no',
        'sanction_plan_date',
        'property_status',
        'total_floors',
        'lease_deed_plot_area',
        'plot_area_matches',
        'plan_plot_area',
        'construction_area',
        'tree_exists',
        'tree_count',
        'remarks',
        'mark_final',
        'signing_authority'
    ];

    // public function breaches()
	// 		{
	// 		    return $this->hasMany(InspectionReportBreach::class,'inspection_report_id');
	// 		}

	// 		public function unauthorisedDetails()
	// 		{
	// 		    return $this->hasMany(InspectionReportUnauthorisedDetail::class,'inspection_report_id' );
	// 		}
       public function breaches()
{
    return $this->hasMany(InspectionReportBreach::class, 'inspection_report_id')
        ->leftJoin('items', 'items.id', '=', 'inspection_report_breaches.breach_floor')
        ->orderByRaw('CAST(items.item_order AS UNSIGNED) ASC')
        ->select('inspection_report_breaches.*');
}

			public function unauthorisedDetails()
{
    return $this->hasMany(InspectionReportUnauthorisedDetail::class, 'inspection_report_id')
        ->leftJoin('items', 'items.id', '=', 'inspection_report_unauthorised_details.floor_breach')
        ->orderByRaw('CAST(items.item_order AS UNSIGNED) ASC')
        ->select('inspection_report_unauthorised_details.*');
}
            public function signingAuthority()
			{
			    return $this->belongsTo(User::class, 'signing_authority', 'id');
			}
            public function seniorofficer()
			{
			    return $this->belongsTo(User::class, 'senior_officer', 'id');
			}
}
   