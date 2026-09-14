<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RollbackApplicationController extends Controller
{
    public function view()
    {
        return view('admin.applications.rollback');
    }

    public function fetch(Request $request)
    {
        $request->validate([
            'application_no' => ['required', 'string'],
        ]);

        $application = DB::table('applications as a')
            ->leftJoin('items as service', 'service.id', '=', 'a.service_type')
            ->leftJoin('items as status', 'status.id', '=', 'a.status')
            ->where('a.application_no', $request->application_no)
            ->select([
                'service.item_name as service_type',
                'status.item_name as status',
            ])
            ->first();

        if (!$application) {
            return response()->json([
                'message' => 'Application not found.',
            ], 404);
        }

        return response()->json([
            'service_type' => $application->service_type,
            'status'       => $application->status,
        ]);
    }

    public function hasAppObj(Request $request)
    {
        $request->validate([
            'application_no' => ['required', 'string'],
        ]);

        $exists = DB::table('application_movements')
            ->where('application_no', $request->application_no)
            ->where('action', 'APP_OBJ')
            ->exists();

        return response()->json([
            'has_app_obj' => $exists,
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'application_no' => ['required', 'string'],
            'reset_to'       => ['required', 'string'],
        ]);

        $appNo   = trim($request->application_no);
        $resetTo = $request->reset_to; // INITIAL_IN_PROGRESS / RECOMMEND_BY_SO

        try {
            DB::transaction(function () use ($appNo, $resetTo) {

                // Always re-fetch from DB (never trust readonly client fields)
                $app = $this->getApplicationWithNames($appNo);

                // ✅ NEW: Allow rollback only when rollback_check = 1
                if ((int) ($app->rollback_check ?? 0) !== 1) {
                    throw new \Exception('Rollback is not allowed for this application.');
                }

                $serviceName = strtolower(trim($app->service_type_name ?? ''));
                $statusName  = strtolower(trim($app->status_name ?? ''));

                // For now: only NOC + Approved
                if ($serviceName !== 'noc' || $statusName !== 'approved') {
                    throw new \Exception('Rollback allowed only for NOC applications in Approved status (for now).');
                }

                // Only two cases for now (NOC):
                switch ($resetTo) {
                    case 'INITIAL_IN_PROGRESS':
                        $this->rollbackNocApprovedToInitialInProgress($app);
                        break;

                    case 'RECOMMEND_BY_SO':
                        $this->rollbackNocApprovedToRecommendBySo($app);
                        break;
                    case 'RESUBMITTED_BY_APPLICANT':
                        $this->rollbackNocApprovedToResubmittedByApplicant($app);
                        break;

                    default:
                        throw new \Exception('This rollback action is not implemented yet.');
                }

                // ✅ NEW: After successful rollback, reset rollback_check back to 0
                DB::table('applications')
                    ->where('application_no', $appNo)
                    ->update(['rollback_check' => 2]);
            });

            return response()->json([
                'message' => 'Rollback completed successfully.',
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Fetch application with service/status names (items.item_name) + ids.
     */
    private function getApplicationWithNames(string $appNo): object
    {
        $app = DB::table('applications as a')
            ->leftJoin('items as service', 'service.id', '=', 'a.service_type')
            ->leftJoin('items as stat', 'stat.id', '=', 'a.status')
            ->where('a.application_no', $appNo)
            ->select([
                'a.application_no',
                'a.service_type',     // item id
                'a.status',           // item id
                'a.rollback_check',   // ✅ NEW: gate column
                'service.item_name as service_type_name',
                'stat.item_name as status_name',
            ])
            ->first();

        if (!$app) {
            throw new \Exception('Application not found.');
        }

        return $app;
    }


    // /**
    //  * Resolve items.id by items.item_code.
    //  */
    // private function getItemIdByCode(string $code): int
    // {
    //     $id = DB::table('items')->where('item_code', $code)->value('id');
    //     if (!$id) {
    //         throw new \Exception("Item code {$code} not found in items table.");
    //     }
    //     return (int) $id;
    // }

    /**
     * CASE 1 (Implemented):
     * NOC + Approved + INITIAL_IN_PROGRESS
     *
     * Steps:
     * 1) delete application_statuses by reg_app_no
     * 2) delete application_movements where status != APP_NEW
     * 3) delete app_latest_actions by application_no
     * 4) update noc_applications.status = APP_IP
     * 5) update applications.status = APP_IP
     * + delete section_mis_histories where service_type = applications.service_type AND model_id = noc_applications.id
     */
    private function rollbackNocApprovedToInitialInProgress(object $app): void
    {
        $appNo = $app->application_no;

        $appNewId = getStatusName('APP_NEW');
        $appIpId  = getStatusName('APP_IP');

        // Find NOC row id (model_id) and delete section_mis_histories
        $nocRow = DB::table('noc_applications')
            ->where('application_no', $appNo)
            ->select(['id'])
            ->first();

        if (!$nocRow) {
            throw new \Exception('NOC application record not found for this application no.');
        }

        DB::table('section_mis_histories')
            ->where('service_type', $app->service_type) // item id from applications
            ->where('model_id', $nocRow->id)
            ->delete();

        DB::table('application_statuses')
            ->where('reg_app_no', $appNo)
            ->delete();

        DB::table('application_movements')
            ->where('application_no', $appNo)
            ->where('status', '!=', $appNewId)
            ->delete();

        DB::table('app_latest_actions')
            ->where('application_no', $appNo)
            ->delete();

        DB::table('noc_applications')
            ->where('application_no', $appNo)
            ->update(['status' => $appIpId]);

        DB::table('applications')
            ->where('application_no', $appNo)
            ->update([
                'status'        => $appIpId,
                'Signed_letter' => null, // adjust case if column is signed_letter
            ]);
    }

/**
 * CASE 2:
 * NOC + Approved + RECOMMEND_BY_SO
 */
    private function rollbackNocApprovedToRecommendBySo(object $app): void
    {
        $appNo = $app->application_no;

        // Fetch latest action FIRST and validate rollback condition
        $latest = DB::table('app_latest_actions')
            ->where('application_no', $appNo)
            ->select([
                'application_no',
                'latest_action',
                'latest_action_by',
                'prev_action',
                'prev_action_by',
            ])
            ->first();

        if (!$latest) {
            throw new \Exception('Latest action record not found for this application no.');
        }

        // Normalize and validate condition BEFORE touching any table
        $prevAction   = strtoupper(trim((string) ($latest->prev_action ?? '')));
        $latestAction = strtoupper(trim((string) ($latest->latest_action ?? '')));

        if ($prevAction !== 'RECOMMENDED' || $latestAction !== 'APPROVE') {
            throw new \Exception(
                'Rollback not allowed: previous_action must be RECOMMENDED and latest_action must be APPROVE.'
            );
        }

        // Resolve required item IDs only after validation passes
        $appAprId = getStatusName('APP_APR');
        $appIpId  = getStatusName('APP_IP');

        // 1) Delete application_movements where status = APP_APR
        DB::table('application_movements')
            ->where('application_no', $appNo)
            ->where('status', $appAprId)
            ->delete();

        // 2) Update noc_applications status = APP_IP
        $updatedNoc = DB::table('noc_applications')
            ->where('application_no', $appNo)
            ->update(['status' => $appIpId]);

        if ($updatedNoc === 0) {
            throw new \Exception('NOC application record not found for this application no.');
        }

        // 3) Update app_latest_actions (move previous → latest, then null previous)
        DB::table('app_latest_actions')
            ->where('application_no', $appNo)
            ->update([
                'latest_action'      => $latest->prev_action,
                'latest_action_by'   => $latest->prev_action_by,
                'prev_action'    => null,
                'prev_action_by' => null,
            ]);

        // 4) Update applications status = APP_IP and Signed_letter = NULL
        $updatedApp = DB::table('applications')
            ->where('application_no', $appNo)
            ->update([
                'status'        => $appIpId,
                'Signed_letter' => null, // adjust case if column is signed_letter
            ]);

        if ($updatedApp === 0) {
            throw new \Exception('Application record not found or could not be updated.');
        }
    }


    /**
     * CASE 3:
     * NOC + Approved + RESUBMITTED_BY_APPLICANT
     *
     * Steps:
     * 1) Update noc_applications.status = APP_IP
     * 2) Update applications.status = APP_IP and Signed_letter = NULL
     * 3) Reset MIS / Scan / Upload checks in latest application_statuses row
     * 4) application_movements cleanup + app_latest_actions update
     * 5) Delete section_mis_histories for this application
     */
    private function rollbackNocApprovedToResubmittedByApplicant(object $app): void
    {
        $appNo   = $app->application_no;
        $appIpId = getStatusName('APP_IP');

        $applicantRoleId      = getRoleIdByName('applicant');
        $sectionOfficerRoleId = getRoleIdByName('section-officer');

        /**
         * Fetch NOC row early (needed for section_mis_histories)
         */
        $nocRow = DB::table('noc_applications')
            ->where('application_no', $appNo)
            ->select('id')
            ->first();

        if (!$nocRow) {
            throw new \Exception('NOC application record not found for this application no.');
        }

        // 1) Update noc_applications status
        DB::table('noc_applications')
            ->where('application_no', $appNo)
            ->update([
                'status' => $appIpId,
            ]);

        // 2) Update applications status + clear signed letter
        $updatedApp = DB::table('applications')
            ->where('application_no', $appNo)
            ->update([
                'status'        => $appIpId,
                'Signed_letter' => null, // change to signed_letter if needed
            ]);

        if ($updatedApp === 0) {
            throw new \Exception('Application record not found or could not be updated.');
        }

        // 3) Reset MIS / Scan / Upload checks in latest application_statuses row
        $latestStatus = DB::table('application_statuses')
            ->where('reg_app_no', $appNo)
            ->orderByDesc('id')
            ->select('id')
            ->first();

        if (!$latestStatus) {
            throw new \Exception('No application status record found for this application no.');
        }

        DB::table('application_statuses')
            ->where('id', $latestStatus->id)
            ->update([
                'is_mis_checked'          => 0,
                'mis_checked_by'          => null,
                'is_scan_file_checked'    => 0,
                'scan_file_checked_by'    => null,
                'is_uploaded_doc_checked' => 0,
                'uploaded_doc_checked_by' => null,
            ]);

        /**
         * 4) application_movements markers + app_latest_actions update
         */

        // latest OBJECT
        $objectRow = DB::table('application_movements')
            ->where('application_no', $appNo)
            ->where('action', 'OBJECT')
            ->orderByDesc('id')
            ->select(['id', 'assigned_by'])
            ->first();

        if (!$objectRow) {
            throw new \Exception("No movement found with action OBJECT for application {$appNo}.");
        }

        // first APP_OBJ after OBJECT
        $appObjRow = DB::table('application_movements')
            ->where('application_no', $appNo)
            ->where('id', '>', $objectRow->id)
            ->where('action', 'APP_OBJ')
            ->orderBy('id')
            ->select(['id', 'assigned_by'])
            ->first();

        if (!$appObjRow) {
            throw new \Exception("No movement found with action APP_OBJ after OBJECT for application {$appNo}.");
        }

        // Update app_latest_actions
        $updatedLatest = DB::table('app_latest_actions')
            ->where('application_no', $appNo)
            ->delete();

        if ($updatedLatest === 0) {
            throw new \Exception('Latest action record not found for this application no.');
        }

        // first applicant -> section-officer assignment after APP_OBJ
        $assignRow = DB::table('application_movements')
            ->where('application_no', $appNo)
            ->where('id', '>', $appObjRow->id)
            ->where('assigned_by_role', $applicantRoleId)
            ->where('assigned_to_role', $sectionOfficerRoleId)
            ->orderBy('id')
            ->select('id')
            ->first();

        if (!$assignRow) {
            throw new \Exception(
                "No movement found after APP_OBJ where applicant assigned to section-officer for application {$appNo}."
            );
        }

        // Delete movements after third marker
        DB::table('application_movements')
            ->where('application_no', $appNo)
            ->where('id', '>', $assignRow->id)
            ->delete();

        /**
         * 5) Delete section_mis_histories
         */
        DB::table('section_mis_histories')
            ->where('service_type', $app->service_type) // item id from applications
            ->where('model_id', $nocRow->id)
            ->delete();
    }



}

