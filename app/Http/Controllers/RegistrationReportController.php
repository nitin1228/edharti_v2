<?php

namespace App\Http\Controllers;

use App\Models\UserRegistration;
use App\Models\Application;
use App\Models\Section;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class RegistrationReportController extends Controller
{
    public function index()
    {
        // Default: Last one week
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(7);

        $sections = Section::where('has_property', 1)->get();
        $itcSection = Section::where('section_code', 'ITC')->first();

        return view('registrations.report', compact('startDate', 'endDate', 'sections', 'itcSection'));
    }

    private function getDateRange($filterType, $startDate, $endDate)
    {
        // Only use custom date range when filter type is 'custom'
        if ($filterType === 'custom' && $startDate && $endDate) {
            return [
                'start' => Carbon::parse($startDate)->startOfDay(),
                'end' => Carbon::parse($endDate)->endOfDay()
            ];
        }

        switch ($filterType) {
            case 'week':
                return [
                    'start' => Carbon::now()->subDays(7)->startOfDay(),
                    'end' => Carbon::now()->endOfDay()
                ];
            case 'month':
                return [
                    'start' => Carbon::now()->subMonth()->startOfMonth()->startOfDay(),
                    'end' => Carbon::now()->subMonth()->endOfMonth()->endOfDay()
                ];
            case 'year':
                return [
                    'start' => Carbon::now()->subYear()->startOfYear()->startOfDay(),
                    'end' => Carbon::now()->subYear()->endOfYear()->endOfDay()
                ];
            default:
                return [
                    'start' => Carbon::now()->subDays(7)->startOfDay(),
                    'end' => Carbon::now()->endOfDay()
                ];
        }
    }

    public function getReportData(Request $request)
    {
        $filterType = $request->input('filter_type', 'week');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Get date range for main data
        $dateRange = $this->getDateRange($filterType, $startDate, $endDate);
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        // Get disposed period based on filter type
        $disposedRange = $this->getDateRange($filterType, $startDate, $endDate);
        $disposedStart = $disposedRange['start'];
        $disposedEnd = $disposedRange['end'];

        // Get all sections with has_property = 1
        $sections = Section::where('has_property', 1)->get();

        // Get ITC section
        $itcSection = Section::where('section_code', 'ITC')->first();

        $reportData = [
            'registration' => [],
            'applications' => []
        ];

        // Process sections for Registration
        $index = 1;
        foreach ($sections as $section) {
            $reportData['registration'][] = $this->calculateRegistrationData($section, $index, $start, $end, $disposedStart, $disposedEnd);
            $index++;
        }
        if ($itcSection) {
            $reportData['registration'][] = $this->calculateRegistrationData($itcSection, $index, $start, $end, $disposedStart, $disposedEnd);
        }

        // Process sections for Applications
        $index = 1;
        foreach ($sections as $section) {
            $reportData['applications'][] = $this->calculateApplicationData($section, $index, $start, $end, $disposedStart, $disposedEnd);
            $index++;
        }
        if ($itcSection) {
            $reportData['applications'][] = $this->calculateApplicationData($itcSection, $index, $start, $end, $disposedStart, $disposedEnd);
        }

        return response()->json([
            'success' => true,
            'data' => $reportData,
            'date_range' => [
                'start' => $start->format('d/m/Y'),
                'end' => $end->format('d/m/Y')
            ]
        ]);
    }

    /**
     * Calculate Registration data for a section
     */
    private function calculateRegistrationData($section, $index, $startDate, $endDate, $disposedStart, $disposedEnd)
    {
        $registrations = UserRegistration::where('section_id', $section->id)
            ->whereHas('statusItem', function ($q) {
                $q->whereIn('item_code', ['RS_NEW', 'RS_PEN', 'RS_APP', 'RS_REJ']);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalPending = $registrations->filter(function ($reg) {
            return in_array($reg->statusItem->item_code ?? '', ['RS_NEW', 'RS_PEN']);
        })->count();

        $totalActivated = $registrations->filter(function ($reg) {
            return ($reg->statusItem->item_code ?? '') === 'RS_APP';
        })->count();

        $lastWeekRejected = $registrations->filter(function ($reg) use ($disposedStart, $disposedEnd) {
            return ($reg->statusItem->item_code ?? '') === 'RS_REJ' &&
                $reg->updated_at >= $disposedStart && $reg->updated_at <= $disposedEnd;
        })->count();

        $lastWeekActivated = $registrations->filter(function ($reg) use ($disposedStart, $disposedEnd) {
            return ($reg->statusItem->item_code ?? '') === 'RS_APP' &&
                $reg->updated_at >= $disposedStart && $reg->updated_at <= $disposedEnd;
        })->count();

        return [
            's_no' => $index,
            'section_name' => $section->name,
            'section_id' => $section->id,
            'total_pending' => $totalPending,
            'total_activated' => $totalActivated,
            'last_week_rejected' => $lastWeekRejected,
            'last_week_activated' => $lastWeekActivated,
        ];
    }

    /**
     * Calculate Application data for a section
     */
    private function calculateApplicationData($section, $index, $startDate, $endDate, $disposedStart, $disposedEnd)
    {
        // Get all applications for this section within date range
        $applications = Application::where('section_id', $section->id)
            ->whereHas('serviceTypeItem', function ($q) {
                $q->whereIn('item_code', ['NOC', 'SUB_MUT']);
            })
            ->whereHas('statusItem', function ($q) {
                $q->whereIn('item_code', ['APP_NEW', 'APP_IP', 'APP_OBJ', 'APP_APR', 'APP_REJ']);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // ----- NOC APPLICATION COUNTS -----
        $nocApplications = $applications->filter(function ($app) {
            return ($app->serviceTypeItem->item_code ?? '') === 'NOC';
        });

        $nocData = $this->calculateApplicationCounts($nocApplications, $disposedStart, $disposedEnd);

        // ----- MUTATION APPLICATION COUNTS -----
        $mutationApplications = $applications->filter(function ($app) {
            return ($app->serviceTypeItem->item_code ?? '') === 'SUB_MUT';
        });

        $mutationData = $this->calculateApplicationCounts($mutationApplications, $disposedStart, $disposedEnd);

        return [
            's_no' => $index,
            'section_name' => $section->name,
            'section_id' => $section->id,
            'noc' => $nocData,
            'mutation' => $mutationData,
        ];
    }

    /**
     * Calculate application counts
     */
    private function calculateApplicationCounts($applications, $disposedStart, $disposedEnd)
    {
        $totalSubmitted = $applications->filter(function ($app) {
            return in_array($app->statusItem->item_code ?? '', ['APP_NEW', 'APP_IP', 'APP_OBJ', 'APP_APR', 'APP_REJ']);
        })->count();

        $totalDisposed = $applications->filter(function ($app) {
            return in_array($app->statusItem->item_code ?? '', ['APP_APR', 'APP_REJ']);
        })->count();

        $totalObjected = $applications->filter(function ($app) {
            return ($app->statusItem->item_code ?? '') === 'APP_OBJ';
        })->count();

        $totalPending = $applications->filter(function ($app) {
            return in_array($app->statusItem->item_code ?? '', ['APP_NEW', 'APP_IP']);
        })->count();

        $totalPendingWithObjected = $applications->filter(function ($app) {
            return in_array($app->statusItem->item_code ?? '', ['APP_NEW', 'APP_IP', 'APP_OBJ']);
        })->count();

        $disposedLastWeek = $applications->filter(function ($app) use ($disposedStart, $disposedEnd) {
            return in_array($app->statusItem->item_code ?? '', ['APP_APR', 'APP_REJ']) &&
                $app->updated_at >= $disposedStart && $app->updated_at <= $disposedEnd;
        })->count();

        return [
            'total_submitted' => $totalSubmitted,
            'total_disposed' => $totalDisposed,
            'total_objected' => $totalObjected,
            'total_pending' => $totalPending,
            'total_pending_with_objected' => $totalPendingWithObjected,
            'disposed_last_week' => $disposedLastWeek,
        ];
    }

    /**
     * Get filter label based on filter type
     */
    private function getFilterLabel($filterType, $startDate, $endDate)
    {
        switch ($filterType) {
            case 'week':
                return 'Last Week';
            case 'month':
                return 'Last Month';
            case 'year':
                return 'Last Year';
            case 'custom':
                return $startDate->format('d/m/Y') . ' to ' . $endDate->format('d/m/Y');
            default:
                return 'Last Week';
        }
    }

    /**
     * Calculate totals for registration data
     */
    private function calculateRegistrationTotals($registrationData)
    {
        $totals = [
            'total_pending' => 0,
            'total_activated' => 0,
            'last_week_rejected' => 0,
            'last_week_activated' => 0
        ];

        foreach ($registrationData as $row) {
            $totals['total_pending'] += $row['total_pending'];
            $totals['total_activated'] += $row['total_activated'];
            $totals['last_week_rejected'] += $row['last_week_rejected'];
            $totals['last_week_activated'] += $row['last_week_activated'];
        }

        return $totals;
    }

    /**
     * Calculate totals for application data
     */
    private function calculateApplicationTotals($applicationData)
    {
        $totals = [
            'noc_total_submitted' => 0,
            'noc_total_disposed' => 0,
            'noc_total_objected' => 0,
            'noc_total_pending' => 0,
            'noc_total_pending_with_objected' => 0,
            'noc_disposed_last_week' => 0,
            'mut_total_submitted' => 0,
            'mut_total_disposed' => 0,
            'mut_total_objected' => 0,
            'mut_total_pending' => 0,
            'mut_total_pending_with_objected' => 0,
            'mut_disposed_last_week' => 0
        ];

        foreach ($applicationData as $row) {
            $totals['noc_total_submitted'] += $row['noc']['total_submitted'];
            $totals['noc_total_disposed'] += $row['noc']['total_disposed'];
            $totals['noc_total_objected'] += $row['noc']['total_objected'];
            $totals['noc_total_pending'] += $row['noc']['total_pending'];
            $totals['noc_total_pending_with_objected'] += $row['noc']['total_pending_with_objected'];
            $totals['noc_disposed_last_week'] += $row['noc']['disposed_last_week'];
            $totals['mut_total_submitted'] += $row['mutation']['total_submitted'];
            $totals['mut_total_disposed'] += $row['mutation']['total_disposed'];
            $totals['mut_total_objected'] += $row['mutation']['total_objected'];
            $totals['mut_total_pending'] += $row['mutation']['total_pending'];
            $totals['mut_total_pending_with_objected'] += $row['mutation']['total_pending_with_objected'];
            $totals['mut_disposed_last_week'] += $row['mutation']['disposed_last_week'];
        }

        return $totals;
    }

    /**
     * Export to PDF - Portrait Layout
     */
    public function exportPdf(Request $request)
    {
        $filterType = $request->input('filter_type', 'week');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Get date range
        $dateRange = $this->getDateRange($filterType, $startDate, $endDate);
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        // Get disposed period
        $disposedRange = $this->getDateRange($filterType, $startDate, $endDate);
        $disposedStart = $disposedRange['start'];
        $disposedEnd = $disposedRange['end'];

        // Get filter label
        $filterLabel = $this->getFilterLabel($filterType, $start, $end);

        // Get all sections with has_property = 1
        $sections = Section::where('has_property', 1)->get();
        $itcSection = Section::where('section_code', 'ITC')->first();

        $registrationData = [];
        $applicationData = [];

        // Process sections for Registration
        $index = 1;
        foreach ($sections as $section) {
            $registrationData[] = $this->calculateRegistrationData($section, $index, $start, $end, $disposedStart, $disposedEnd);
            $index++;
        }
        if ($itcSection) {
            $registrationData[] = $this->calculateRegistrationData($itcSection, $index, $start, $end, $disposedStart, $disposedEnd);
        }

        // Process sections for Applications
        $index = 1;
        foreach ($sections as $section) {
            $applicationData[] = $this->calculateApplicationData($section, $index, $start, $end, $disposedStart, $disposedEnd);
            $index++;
        }
        if ($itcSection) {
            $applicationData[] = $this->calculateApplicationData($itcSection, $index, $start, $end, $disposedStart, $disposedEnd);
        }

        // Calculate totals
        $registrationTotals = $this->calculateRegistrationTotals($registrationData);
        $applicationTotals = $this->calculateApplicationTotals($applicationData);

        $dateRangeArray = [
            'start' => $start->format('d/m/Y'),
            'end' => $end->format('d/m/Y')
        ];

        $pdf = Pdf::loadView('registrations.pdf', [
            'registrationData' => $registrationData,
            'applicationData' => $applicationData,
            'registrationTotals' => $registrationTotals,
            'applicationTotals' => $applicationTotals,
            'dateRange' => $dateRangeArray,
            'filterType' => $filterType,
            'filterLabel' => $filterLabel,
        ]);

        // Set portrait layout with A4 size
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('section-wise-report-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export to CSV
     */
    public function exportCsv(Request $request)
    {
        $filterType = $request->input('filter_type', 'week');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Get date range
        $dateRange = $this->getDateRange($filterType, $startDate, $endDate);
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        // Get disposed period
        $disposedRange = $this->getDateRange($filterType, $startDate, $endDate);
        $disposedStart = $disposedRange['start'];
        $disposedEnd = $disposedRange['end'];

        // Get filter label
        $filterLabel = $this->getFilterLabel($filterType, $start, $end);

        // Get all sections with has_property = 1
        $sections = Section::where('has_property', 1)->get();
        $itcSection = Section::where('section_code', 'ITC')->first();

        $registrationData = [];
        $applicationData = [];

        // Process sections for Registration
        $index = 1;
        foreach ($sections as $section) {
            $registrationData[] = $this->calculateRegistrationData($section, $index, $start, $end, $disposedStart, $disposedEnd);
            $index++;
        }
        if ($itcSection) {
            $registrationData[] = $this->calculateRegistrationData($itcSection, $index, $start, $end, $disposedStart, $disposedEnd);
        }

        // Process sections for Applications
        $index = 1;
        foreach ($sections as $section) {
            $applicationData[] = $this->calculateApplicationData($section, $index, $start, $end, $disposedStart, $disposedEnd);
            $index++;
        }
        if ($itcSection) {
            $applicationData[] = $this->calculateApplicationData($itcSection, $index, $start, $end, $disposedStart, $disposedEnd);
        }

        // Calculate totals
        $registrationTotals = $this->calculateRegistrationTotals($registrationData);
        $applicationTotals = $this->calculateApplicationTotals($applicationData);

        $dateRangeArray = [
            'start' => $start->format('d/m/Y'),
            'end' => $end->format('d/m/Y')
        ];

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="section-wise-report-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($registrationData, $applicationData, $registrationTotals, $applicationTotals, $dateRangeArray, $filterLabel) {
            $handle = fopen('php://output', 'w');

            // Add title and date range
            fputcsv($handle, ['Section Wise Report']);
            fputcsv($handle, ['Date Range: ' . $dateRangeArray['start'] . ' to ' . $dateRangeArray['end']]);
            fputcsv($handle, ['Filter: ' . $filterLabel]);
            fputcsv($handle, ['']);

            // Add Registration Summary header
            fputcsv($handle, ['Registration Summary']);
            fputcsv($handle, ['']);
            fputcsv($handle, ['S.No', 'Section Name', 'Total Pending', 'Total Activated', 'Total Rejected (' . $filterLabel . ')', 'Total Approved (' . $filterLabel . ')']);

            foreach ($registrationData as $row) {
                fputcsv($handle, [
                    $row['s_no'],
                    $row['section_name'],
                    $row['total_pending'],
                    $row['total_activated'],
                    $row['last_week_rejected'],
                    $row['last_week_activated']
                ]);
            }

            // Add Registration Totals row
            fputcsv($handle, [
                'Total',
                '',
                $registrationTotals['total_pending'],
                $registrationTotals['total_activated'],
                $registrationTotals['last_week_rejected'],
                $registrationTotals['last_week_activated']
            ]);

            fputcsv($handle, ['']);
            fputcsv($handle, ['']);

            // Add Applications header with 6 columns for each type
            fputcsv($handle, ['Section Wise Report - Applications']);
            fputcsv($handle, ['']);

            // NOC Applications header - 6 columns
            fputcsv($handle, ['NOC APPLICATIONS']);
            fputcsv($handle, ['S.No', 'Section Name', 'Total Submitted', 'Total Disposed', 'Total Objected', 'Total Pending', 'Total Pending (With Objected)', 'Disposed (' . $filterLabel . ')']);

            foreach ($applicationData as $row) {
                fputcsv($handle, [
                    $row['s_no'],
                    $row['section_name'],
                    $row['noc']['total_submitted'],
                    $row['noc']['total_disposed'],
                    $row['noc']['total_objected'],
                    $row['noc']['total_pending'],
                    $row['noc']['total_pending_with_objected'],
                    $row['noc']['disposed_last_week']
                ]);
            }

            // Add NOC Totals row
            fputcsv($handle, [
                'Total',
                '',
                $applicationTotals['noc_total_submitted'],
                $applicationTotals['noc_total_disposed'],
                $applicationTotals['noc_total_objected'],
                $applicationTotals['noc_total_pending'],
                $applicationTotals['noc_total_pending_with_objected'],
                $applicationTotals['noc_disposed_last_week']
            ]);

            fputcsv($handle, ['']);
            fputcsv($handle, ['']);

            // Mutation Applications header - 6 columns
            fputcsv($handle, ['MUTATION APPLICATIONS']);
            fputcsv($handle, ['S.No', 'Section Name', 'Total Submitted', 'Total Disposed', 'Total Objected', 'Total Pending', 'Total Pending (With Objected)', 'Disposed (' . $filterLabel . ')']);

            foreach ($applicationData as $row) {
                fputcsv($handle, [
                    $row['s_no'],
                    $row['section_name'],
                    $row['mutation']['total_submitted'],
                    $row['mutation']['total_disposed'],
                    $row['mutation']['total_objected'],
                    $row['mutation']['total_pending'],
                    $row['mutation']['total_pending_with_objected'],
                    $row['mutation']['disposed_last_week']
                ]);
            }

            // Add Mutation Totals row
            fputcsv($handle, [
                'Total',
                '',
                $applicationTotals['mut_total_submitted'],
                $applicationTotals['mut_total_disposed'],
                $applicationTotals['mut_total_objected'],
                $applicationTotals['mut_total_pending'],
                $applicationTotals['mut_total_pending_with_objected'],
                $applicationTotals['mut_disposed_last_week']
            ]);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Print Registration Table Only
     */
    public function printRegistration(Request $request)
    {
        $filterType = $request->input('filter_type', 'week');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Get date range
        $dateRange = $this->getDateRange($filterType, $startDate, $endDate);
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        // Get disposed period
        $disposedRange = $this->getDateRange($filterType, $startDate, $endDate);
        $disposedStart = $disposedRange['start'];
        $disposedEnd = $disposedRange['end'];

        $filterLabel = $this->getFilterLabel($filterType, $start, $end);

        $sections = Section::where('has_property', 1)->get();
        $itcSection = Section::where('section_code', 'ITC')->first();

        $registrationData = [];
        $index = 1;

        foreach ($sections as $section) {
            $registrationData[] = $this->calculateRegistrationData($section, $index, $start, $end, $disposedStart, $disposedEnd);
            $index++;
        }
        if ($itcSection) {
            $registrationData[] = $this->calculateRegistrationData($itcSection, $index, $start, $end, $disposedStart, $disposedEnd);
        }

        $registrationTotals = $this->calculateRegistrationTotals($registrationData);

        $dateRangeArray = [
            'start' => $start->format('d/m/Y'),
            'end' => $end->format('d/m/Y')
        ];

        return view('registrations.print-registration', compact('registrationData', 'registrationTotals', 'dateRangeArray', 'filterLabel'));
    }

    /**
     * Print Application Table Only
     */
    public function printApplication(Request $request)
    {
        $filterType = $request->input('filter_type', 'week');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Get date range
        $dateRange = $this->getDateRange($filterType, $startDate, $endDate);
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        // Get disposed period
        $disposedRange = $this->getDateRange($filterType, $startDate, $endDate);
        $disposedStart = $disposedRange['start'];
        $disposedEnd = $disposedRange['end'];

        $filterLabel = $this->getFilterLabel($filterType, $start, $end);

        $sections = Section::where('has_property', 1)->get();
        $itcSection = Section::where('section_code', 'ITC')->first();

        $applicationData = [];
        $index = 1;

        foreach ($sections as $section) {
            $applicationData[] = $this->calculateApplicationData($section, $index, $start, $end, $disposedStart, $disposedEnd);
            $index++;
        }
        if ($itcSection) {
            $applicationData[] = $this->calculateApplicationData($itcSection, $index, $start, $end, $disposedStart, $disposedEnd);
        }

        $applicationTotals = $this->calculateApplicationTotals($applicationData);

        $dateRangeArray = [
            'start' => $start->format('d/m/Y'),
            'end' => $end->format('d/m/Y')
        ];

        return view('registrations.print-application', compact('applicationData', 'applicationTotals', 'dateRangeArray', 'filterLabel'));
    }

    /**
     * Print Both Registration and Application Tables
     */
    public function printBoth(Request $request)
    {
        $filterType = $request->input('filter_type', 'week');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Get date range
        $dateRange = $this->getDateRange($filterType, $startDate, $endDate);
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        // Get disposed period
        $disposedRange = $this->getDateRange($filterType, $startDate, $endDate);
        $disposedStart = $disposedRange['start'];
        $disposedEnd = $disposedRange['end'];

        $filterLabel = $this->getFilterLabel($filterType, $start, $end);

        $sections = Section::where('has_property', 1)->get();
        $itcSection = Section::where('section_code', 'ITC')->first();

        $registrationData = [];
        $applicationData = [];

        // Process sections for Registration
        $index = 1;
        foreach ($sections as $section) {
            $registrationData[] = $this->calculateRegistrationData($section, $index, $start, $end, $disposedStart, $disposedEnd);
            $index++;
        }
        if ($itcSection) {
            $registrationData[] = $this->calculateRegistrationData($itcSection, $index, $start, $end, $disposedStart, $disposedEnd);
        }

        // Process sections for Applications
        $index = 1;
        foreach ($sections as $section) {
            $applicationData[] = $this->calculateApplicationData($section, $index, $start, $end, $disposedStart, $disposedEnd);
            $index++;
        }
        if ($itcSection) {
            $applicationData[] = $this->calculateApplicationData($itcSection, $index, $start, $end, $disposedStart, $disposedEnd);
        }

        $registrationTotals = $this->calculateRegistrationTotals($registrationData);
        $applicationTotals = $this->calculateApplicationTotals($applicationData);

        $dateRangeArray = [
            'start' => $start->format('d/m/Y'),
            'end' => $end->format('d/m/Y')
        ];

        return view('registrations.print-both', compact('registrationData', 'applicationData', 'registrationTotals', 'applicationTotals', 'dateRangeArray', 'filterLabel'));
    }

    public function registrationSummaryDetails(Request $request)
    {
        $user = Auth::user();
        $filters = $request->all();

        // Get filter section IDs
        $filterSectionIds = [];
        if (isset($filters['section_id'])) {
            $filterSectionIds = [$filters['section_id']];
        } else if (!$user->hasAnyRole(['super-admin', 'lndo', 'minister'])) {
            $filterSectionIds = $user->sections->pluck('id')->toArray();
        }

        // Get filter status
        $filterStatus = [];
        if (isset($filters['status'])) {
            $normalizedStatus = preg_replace('/\s*,\s*/', ',', $filters['status']);
            $filterStatus = array_map('trim', explode(',', $normalizedStatus));
        }

        // Get filter dates
        $filterDateFrom = $filters['date_from'] ?? null;
        $filterDateTo = $filters['date_to'] ?? null;

        // Build query
        $query = UserRegistration::query()
            ->with(['statusItem', 'section', 'oldColony'])
            ->leftJoin('items', 'user_registrations.status', '=', 'items.id')
            ->leftJoin('sections', 'user_registrations.section_id', '=', 'sections.id')
            ->leftJoin('old_colonies', 'user_registrations.locality', '=', 'old_colonies.id')
            ->select(
                'user_registrations.*',
                'items.item_name as status_name',
                'items.item_code as status_code',
                'sections.name as section_name',
                'sections.section_code',
                'old_colonies.name as colony_name'
            );

        // Apply section filter
        if (!empty($filterSectionIds)) {
            $query->whereIn('user_registrations.section_id', $filterSectionIds);
        }

        // Apply status filter
        if (!empty($filterStatus)) {
            $query->whereIn('items.item_code', $filterStatus);
        }

        // Apply date filters
        if (!is_null($filterDateFrom)) {
            $query->whereDate('user_registrations.created_at', '>=', Carbon::createFromFormat('d-m-Y', $filterDateFrom)->format('Y-m-d'));
        }
        if (!is_null($filterDateTo)) {
            $query->whereDate('user_registrations.created_at', '<=', Carbon::createFromFormat('d-m-Y', $filterDateTo)->format('Y-m-d'));
        }

        $registrations = $query->orderBy('user_registrations.created_at', 'DESC')->get();

        $data['registrations'] = $registrations;
        return view('registrations.summary-details', $data);
    }
}
