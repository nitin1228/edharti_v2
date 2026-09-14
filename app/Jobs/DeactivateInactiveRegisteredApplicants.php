<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;

class DeactivateInactiveRegisteredApplicants implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        Log::info("Job is running new");
        $users = User::where('user_type', 'applicant')->get();
        $currentDate = Carbon::now();
        $maxInactiveDays = config('constants.MAX_INACTIVE_DAYS_FOR_REGISTERED_USER');
        Log::info("Job is running 210");

        // Get status IDs from items table
        $regAppId = Item::where('item_code', 'RS_APP')->value('id');
        $regRejId = Item::where('item_code', 'RS_REJ')->value('id');
        $appAprId = Item::where('item_code', 'APP_APR')->value('id');
        $appRejId = Item::where('item_code', 'APP_REJ')->value('id');
        
        Log::info("Status IDs - RS_APP: {$regAppId}, RS_REJ: {$regRejId}, APP_APR: {$appAprId}, APP_REJ: {$appRejId}");

        foreach ($users as $user) {
            $daysSinceCreation = $user->created_at->diffInDays($currentDate);

            if (!$user->applications()->exists() && $daysSinceCreation > $maxInactiveDays) {
                // $applicantNumber = $user->applicantDetails?->applicant_number;
                /*$applicantNumber = 'APL0000318';
                
                if (empty($applicantNumber)) {
                    Log::warning("No applicant_number found in applicant_user_details for user_id: {$user->id}");
                    continue;
                }

                Log::info("Checking applicant: {$applicantNumber}");

                $hasPending = $this->hasPendingAdditionalProperties($applicantNumber, $regAppId, $regRejId);

                Log::info("Has Pending Additional Properties: " . ($hasPending ? 'YES' : 'NO'));
                
                // Check if there are additional properties with status RS_APP that have applications not in APP_APR or APP_REJ
                $hasActiveAdditionalProperties = $this->hasActiveAdditionalProperties($applicantNumber, $regAppId, $appAprId, $appRejId);
                
                // Check if there are additional properties with status not RS_APP or not RS_REJ
                $hasPendingAdditionalProperties = $this->hasPendingAdditionalProperties($applicantNumber, $regAppId, $regRejId);
                
                // Skip deletion if there are active or pending additional properties
                if ($hasActiveAdditionalProperties || $hasPendingAdditionalProperties) {
                    Log::info("User {$user->id} has active/pending additional properties, skipping deactivation");
                    continue;
                }
                
                DB::beginTransaction();
                try {
                    $user->status = 0;
                    $user->save();
                    $user->userProperties()->delete();

                    // Soft delete matching registration(s)
                    \App\Models\UserRegistration::where('applicant_number', $applicantNumber)->delete();
                    Log::info("UserRegistration soft-deleted for applicant_number: {$applicantNumber}, user_id: {$user->id}");
                    
                    // Soft delete expired RS_APP additional properties
                    $this->deleteExpiredAdditionalProperties($applicantNumber, $maxInactiveDays, $regAppId, $appAprId, $appRejId);

                    if ($user->delete()) {
                        Log::info("User ({$user->id}) deactivated (no applications & inactive).");
                        DB::commit();
                    } else {
                        Log::warning("User ({$user->id}) soft deletion failed.");
                        DB::rollBack();
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error("Failed deactivating user ({$user->id}): " . $e->getMessage());
                }*/
                    $applicantNumber = $user->applicantDetails?->applicant_number;

                    if (empty($applicantNumber)) {
                        Log::warning("No applicant_number found in applicant_user_details for user_id: {$user->id}");
                        continue;
                    }

                    if ($applicantNumber !== 'APL0000318') {
                        continue;
                    }

                    Log::info("START applicant {$applicantNumber}, user_id {$user->id}");

                    $hasActiveAdditionalProperties = $this->hasActiveAdditionalProperties($applicantNumber, $regAppId, $appAprId, $appRejId);
                    $hasPendingAdditionalProperties = $this->hasPendingAdditionalProperties($applicantNumber, $regAppId, $regRejId);

                    Log::info("Applicant {$applicantNumber}: hasActiveAdditionalProperties=" . ($hasActiveAdditionalProperties ? 'YES' : 'NO'));
                    Log::info("Applicant {$applicantNumber}: hasPendingAdditionalProperties=" . ($hasPendingAdditionalProperties ? 'YES' : 'NO'));

                    if ($hasActiveAdditionalProperties || $hasPendingAdditionalProperties) {
                        Log::info("Applicant {$applicantNumber}: SKIPPING deactivation");
                        break;
                    }

                    Log::info("Applicant {$applicantNumber}: ENTERING deactivation block");

                    /* DB::beginTransaction();
                    try {
                        Log::info("Applicant {$applicantNumber}: setting status=0");
                        $user->status = 0;
                        $user->save();

                        Log::info("Applicant {$applicantNumber}: deleting userProperties");
                        $user->userProperties()->delete();

                        Log::info("Applicant {$applicantNumber}: deleting registrations");
                        \App\Models\UserRegistration::where('applicant_number', $applicantNumber)->delete();

                        Log::info("Applicant {$applicantNumber}: deleting expired additional properties");
                        $this->deleteExpiredAdditionalProperties($applicantNumber, $maxInactiveDays, $regAppId, $appAprId, $appRejId);

                        Log::info("Applicant {$applicantNumber}: deleting user");
                        if ($user->delete()) {
                            Log::info("Applicant {$applicantNumber}: COMMIT");
                            DB::commit();
                        } else {
                            Log::warning("Applicant {$applicantNumber}: delete failed, rollback");
                            DB::rollBack();
                        }
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::error("Applicant {$applicantNumber}: exception " . $e->getMessage());
                    } */
            }
        }
    }
    
    /**
     * Check if user has additional properties with status RS_APP that have
     * applications linked with non-final status (not APP_APR or APP_REJ)
     */
    protected function hasActiveAdditionalProperties($applicantNumber, $regAppId, $appAprId, $appRejId)
    {
        if (empty($applicantNumber) || empty($regAppId)) {
            return false;
        }
        
        $additionalProperties = \App\Models\NewlyAddedProperty::where('applicant_number', $applicantNumber)
            ->where('status', $regAppId)
            ->get();
        
        foreach ($additionalProperties as $property) {
            if ($this->hasNonFinalApplication($property->old_property_id, $appAprId, $appRejId)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if a property has any application with non-final status
     */
    protected function hasNonFinalApplication($oldPropertyId, $appAprId, $appRejId)
    {
        if (empty($oldPropertyId)) {
            return false;
        }
        
        // Get all applications
        $applications = \App\Models\Application::all();
        
        foreach ($applications as $application) {
            $serviceType = $application->service_type;
            $modelId = $application->model_id;
            
            if ($serviceType) {
                $item = Item::find($serviceType);
                
                if ($item && $item->item_code == 'NOC') {
                    $nocApplication = \App\Models\NocApplication::find($modelId);
                    if ($nocApplication && $nocApplication->old_property_id == $oldPropertyId) {
                        if (!in_array($application->status, [$appAprId, $appRejId])) {
                            return true;
                        }
                    }
                } elseif ($item && $item->item_code == 'SUB_MUT') {
                    $mutationApplication = \App\Models\MutationApplication::find($modelId);
                    if ($mutationApplication && $mutationApplication->old_property_id == $oldPropertyId) {
                        if (!in_array($application->status, [$appAprId, $appRejId])) {
                            return true;
                        }
                    }
                }
            }
        }
        
        return false;
    }
    
    /**
     * Check if user has additional properties with any pending status that requires keeping the user active
     * Statuses that are not RS_APP or RS_REJ should keep the user active
     */
    protected function hasPendingAdditionalProperties($applicantNumber, $regAppId, $regRejId)
    {
        if (empty($applicantNumber)) {
            return false;
        }
        
        return \App\Models\NewlyAddedProperty::where('applicant_number', $applicantNumber)
            ->whereNotIn('status', [$regAppId, $regRejId])
            ->exists();
    }
    
    /**
     * Delete expired RS_APP additional properties that have no applications or have applications with final status
     */
    protected function deleteExpiredAdditionalProperties($applicantNumber, $maxInactiveDays, $regAppId, $appAprId, $appRejId)
    {
        if (empty($applicantNumber) || empty($regAppId)) {
            return;
        }
        
        $cutoffDate = Carbon::now()->subDays($maxInactiveDays);
        
        $additionalProperties = \App\Models\NewlyAddedProperty::where('applicant_number', $applicantNumber)
            ->where('status', $regAppId)
            ->where('updated_at', '<=', $cutoffDate)
            ->get();
        
        foreach ($additionalProperties as $property) {
            $hasFinalStatusOnly = $this->hasOnlyFinalStatusApplications($property->old_property_id, $appAprId, $appRejId);
            
            if ($hasFinalStatusOnly) {
                $property->delete();
                Log::info("Soft deleted additional property ID: {$property->id} for applicant_number: {$applicantNumber}");
            }
        }
    }
    
    /**
     * Check if property has no applications or only applications with final status (APP_APR or APP_REJ)
     */
    protected function hasOnlyFinalStatusApplications($oldPropertyId, $appAprId, $appRejId)
    {
        if (empty($oldPropertyId)) {
            return true;
        }
        
        $hasAnyApplication = false;
        $hasNonFinalApplication = false;
        
        // Get all applications
        $applications = \App\Models\Application::all();
        
        foreach ($applications as $application) {
            $serviceType = $application->service_type;
            $modelId = $application->model_id;
            
            if ($serviceType) {
                $item = Item::find($serviceType);
                
                if ($item && $item->item_code == 'NOC') {
                    $nocApplication = \App\Models\NocApplication::find($modelId);
                    if ($nocApplication && $nocApplication->old_property_id == $oldPropertyId) {
                        $hasAnyApplication = true;
                        if (!in_array($application->status, [$appAprId, $appRejId])) {
                            $hasNonFinalApplication = true;
                            break;
                        }
                    }
                } elseif ($item && $item->item_code == 'SUB_MUT') {
                    $mutationApplication = \App\Models\MutationApplication::find($modelId);
                    if ($mutationApplication && $mutationApplication->old_property_id == $oldPropertyId) {
                        $hasAnyApplication = true;
                        if (!in_array($application->status, [$appAprId, $appRejId])) {
                            $hasNonFinalApplication = true;
                            break;
                        }
                    }
                }
            }
        }
        
        // Return true if no applications OR only final status applications
        return !$hasAnyApplication || !$hasNonFinalApplication;
    }
}