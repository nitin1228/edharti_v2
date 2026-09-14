<?php

namespace App\Services;

use App\Models\Application;
use App\Models\ApplicationMovement;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApplicationForwardService
{
    /**
     * Forward application to department
     */
    public function forward(array $data): array
    {
        // 1. Resolve role
        $role = Role::where('name', $data['forwardTo'])->first();

        if (!$role) {
            return $this->failure('Role does not exist.', 404);
        }

        // 2. Fetch application
        $application = Application::where('application_no', $data['applicantNo'])->first();

        if (!$application) {
            return $this->failure('Application not found.', 404);
        }

        $assignedToUser = $this->resolveUserForRole(
            $application->section_id,
            $role->id
        );

        if (!$assignedToUser) {
            return $this->failure('User does not exist for given role.', 404);
        }

        $isForwarded = (!empty($data['isNewDemand']) && $data['isNewDemand'] == 1) ? 2 : 1;

        $movement = ApplicationMovement::create([
            'assigned_by'        => Auth::id(),
            'assigned_by_role'   => Auth::user()->roles[0]->id ?? null,
            'assigned_to'        => $assignedToUser,
            'assigned_to_role'   => $role->id,
            'service_type'       => $data['serviceType'],
            'model_id'           => $data['modalId'],
            'status'             => $application->status,
            'action'             => null,
            'application_no'     => $data['applicantNo'],
            'remarks'            => $data['forwardRemark'],
            'is_forwarded'       => $isForwarded,
        ]);

        if (!$movement) {
            return $this->failure('Error forwarding the application.', 500);
        }

        return $this->success('Application forwarded successfully!');
    }

    /**
     * Revert application to previous assignee
     */
    public function revert(array $data): array
    {
        $lastMovement = DB::table('application_movements')
            ->where('model_id', $data['modalId'])
            ->where('application_no', $data['applicantNo'])
            ->where('assigned_to', Auth::id())
            ->where('is_forwarded', '>', 0)
            ->orderByDesc('id')
            ->first();
        if (!$lastMovement) {
            return $this->failure('No application movement record found.', 404);
        }

        // Mark previous forward inactive
        DB::table('application_movements')
            ->where('id', $lastMovement->id)
            ->update(['is_forwarded' => 0]);

        $movement = ApplicationMovement::create([
            'assigned_by'        => Auth::id(),
            'assigned_by_role'   => Auth::user()->roles[0]->id ?? null,
            'assigned_to'        => $lastMovement->assigned_by,
            'assigned_to_role'   => $lastMovement->assigned_by_role,
            'service_type'       => $data['serviceType'],
            'model_id'           => $data['modalId'],
            'status'             => $lastMovement->status,
            'action'             => $lastMovement->action,
            'application_no'     => $data['applicantNo'],
            'remarks'            => $data['revertRemark'],
            'is_forwarded'       => 0,
        ]);

        if (!$movement) {
            return $this->failure('Error reverting the application.', 500);
        }

        return $this->success('Application successfully reverted to assignee!');
    }

    /**
     * Resolve user for role within section
     */
    private function resolveUserForRole(int $sectionId, int $roleId): ?int
    {
        if ($roleId == 11) {
            $row = DB::table('model_has_roles')->where('role_id', $roleId)->first();
            return $row->model_id;
        }
        $assignedToUser = DB::table('section_user as su')
            ->join('model_has_roles as mhr', 'su.user_id', '=', 'mhr.model_id')
            ->where('mhr.role_id', $roleId)
            ->where('su.section_id', $sectionId)
            ->select('su.*', 'mhr.role_id') // Select specific columns if needed
            ->first();
        return !empty($assignedToUser) ? $assignedToUser->user_id : null;
    }

    private function success(string $message): array
    {
        return [
            'status' => 'success',
            'message' => $message,
            'code' => 200
        ];
    }

    private function failure(string $message, int $code): array
    {
        return [
            'status' => 'failure',
            'message' => $message,
            'code' => $code
        ];
    }
}
