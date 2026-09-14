<?php

namespace App\Http\Middleware;

use App\Models\Application;
use App\Models\Item;
use App\Models\User;
use App\Models\UserRegistration;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class OldRegistrationsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip middleware for specific routes
        if ($request->routeIs([
            'register.user.details',
            'applications.view'
        ])) {
            if (Session::has('show_old_records_popup')) {
                Session::put('hide_old_records_popup', true);
                return $next($request);
            }
        }

        if (auth()->check() && auth()->user()->hasRole('section-officer')) {

            // Get user's sections once
            $userSections = DB::table('section_user')
                ->where('user_id', Auth::id())
                ->pluck('section_id');

            if ($userSections->isNotEmpty()) {

                // Process Registrations
                $statusIds = Item::whereIn('item_code', ['RS_PEN', 'RS_REW', 'RS_UREW', 'RS_NEW'])->pluck('id');

                $registrations = UserRegistration::when($statusIds->isNotEmpty(), function ($query) use ($userSections, $statusIds) {
                    return $query->whereIn('section_id', $userSections)
                        ->whereIn('status', $statusIds)
                        ->where('updated_at', '<=', now()->subDays(1000))
                        ->whereNull('deleted_at')
                        ->whereNull('pending_remark');
                })
                    ->get();

                // Process Applications
                $appStatusIds = Item::whereIn('item_code', ['APP_NEW', 'APP_PEN', 'APP_IP', 'APP_HOLD'])->pluck('id');

                $applications = collect();
                $userIds = [];

                if ($appStatusIds->isNotEmpty()) {
                    $applications = Application::whereIn('applications.section_id', $userSections)
                        ->whereIn('applications.status', $appStatusIds)
                        ->whereNull('applications.disposed_at')
                        ->whereNull('applications.pending_remark')
                        ->join(
                            DB::raw('(SELECT application_no, MAX(updated_at) as latest_updated_at, assigned_to 
                  FROM application_movements 
                  WHERE assigned_to = ' . Auth::id() . ' 
                  GROUP BY application_no, assigned_to) as latest_movement'),
                            function ($join) {
                                $join->on('applications.application_no', '=', 'latest_movement.application_no');
                            }
                        )
                        ->where('latest_movement.latest_updated_at', '<=', now()->subDays(1000))
                        ->select('applications.*', 'latest_movement.latest_updated_at as movement_updated_at')
                        ->get();

                    // Collect all user IDs from applications
                    $userIds = $applications->pluck('created_by')->unique()->filter()->values()->toArray();
                }

                // Fetch all users in one query
                $users = User::whereIn('id', $userIds)->get()->keyBy('id');

                $allOldRecords = [];

                // Add registrations to collection
                foreach ($registrations as $registration) {
                    $allOldRecords[] = [
                        'type' => 'registration',
                        'type_label' => 'Registration',
                        'id' => $registration->id,
                        'identifier' => $registration->applicant_number,
                        'identifier_label' => 'Applicant Number',
                        'name' => $registration->name,
                        'name_label' => 'Applicant Name',
                        'created_at' => $registration->created_at,
                        'pending_remark' => $registration->pending_remark,
                        'section_id' => $registration->section_id
                    ];
                }

                // Add applications to collection with user names
                foreach ($applications as $application) {
                    $userName = isset($users[$application->created_by]) ? $users[$application->created_by]->name : 'N/A';

                    $allOldRecords[] = [
                        'type' => 'application',
                        'type_label' => 'Application',
                        'id' => $application->id,
                        'model_id' => $application->model_id,
                        'model_name' => $application->model_name,
                        'identifier' => $application->application_no,
                        'identifier_label' => 'Application Number',
                        'name' => $userName,
                        'name_label' => 'Applicant Name',
                        'created_at' => $application->movement_updated_at, // This will now be the application_movements.updated_at value
                        'pending_remark' => $application->pending_remark,
                        'section_id' => $application->section_id,
                        'created_by' => $application->created_by
                    ];
                }



                // Handle popup data
                $hasAnyOldRecords = !empty($allOldRecords);
                if ($hasAnyOldRecords && !Session::has('old_records_popup_data')) {
                    Session::put('old_records_data', $allOldRecords);
                    Session::put('show_old_records_popup', true);
                } elseif (!$hasAnyOldRecords) {
                    Session::forget(['old_records_data', 'show_old_records_popup', 'old_records_popup_data']);
                }
            }
        }

        return $next($request);
    }
}
