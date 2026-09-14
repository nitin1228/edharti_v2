<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Item;
use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Helpers\GeneralFunctions;
use Carbon\Carbon;

class DeactivateUsersWithInactiveApplications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $currentDate = Carbon::now();
        $statusIds = Item::whereIn('item_code', ['APP_APR', 'APP_REJ'])->pluck('id')->toArray();
        $applications = Application::whereIn('status', $statusIds)->get();
        $maxInactiveDays = config('constants.MAX_INACTIVE_DAYS_AFTER_APPLICATION_DISPOSED');

        foreach ($applications as $application) {
            $daysSinceUpdate = $application->updated_at->diffInDays($currentDate);
            if ($daysSinceUpdate > $maxInactiveDays) {
                $user = User::find($application->created_by);

                if ($user) {
                    $hasActiveApplications = Application::where('created_by', $user->id)
                        ->whereNotIn('status', $statusIds)
                        ->exists();

                    if (!$hasActiveApplications) {
                        $applicantNumber = $user->applicantDetails?->applicant_number;
                        
                        // Check if there are additional properties with status REG_APP that have applications not in APP_APR or APP_REJ
                        $hasActiveAdditionalProperties = $this->hasActiveAdditionalProperties($applicantNumber);
                        
                        // Check if there are additional properties with status not REG_APP or not REG_REJ
                        $hasPendingAdditionalProperties = $this->hasPendingAdditionalProperties($applicantNumber);
                        
                        // Skip deletion if there are active or pending additional properties
                        if ($hasActiveAdditionalProperties || $hasPendingAdditionalProperties) {
                            continue;
                        }
                        
                        // Get user's properties
                        $userProperties = $user->userProperties;

                        // Get the 'DEM_PENDING' item ID
                        $pendingDemandStatusIds = Item::whereIn('item_code', ['DEM_PENDING', 'DEM_DRAFT'])->pluck('id')->toArray();

                        // Check if any property has a pending demand
                        $hasPendingDemand = false;

                        foreach ($userProperties as $property) {
                            $demandExists = \App\Models\Demand::where('property_master_id', $property->new_property_id)
                                ->whereIn('status', $pendingDemandStatusIds)
                                ->exists();

                            if ($demandExists) {
                                $hasPendingDemand = true;
                                break;
                            }
                        }

                        // Skip user deactivation if pending demand exists
                        if ($hasPendingDemand) {
                            continue;
                        }

                        // Proceed with deactivation
                        DB::beginTransaction();
                        try {
                            $$user->status = 0;
                            $user->save();

                            // Delete draft temp applications linked with user's properties before unlinking properties
                            $this->deleteUserDraftTempApplications($user);

                            // Now unlink properties
                            $user->userProperties()->delete();

                            // Soft delete user_registrations record using applicant_number from applicant_user_details
                            if (!empty($applicantNumber)) {
                                \App\Models\UserRegistration::where('applicant_number', $applicantNumber)->delete();
                                Log::info("UserRegistration soft-deleted for applicant_number: {$applicantNumber}, user_id: {$user->id}");
                                
                                // Soft delete REG_APP additional properties that have no applications or have applications with final status
                                // and have updated_at exceeding max inactive days
                                $this->deleteExpiredAdditionalProperties($applicantNumber, $maxInactiveDays);
                            } else {
                                Log::warning("No applicant_number found for user_id: {$user->id} while deactivating (inactive application).");
                            }

                            if ($user->delete()) {
                                Log::info("User ({$user->id}) deactivated (application inactive 30+ days, no pending demands).");
                                DB::commit();
                            } else {
                                Log::warning("User ({$user->id}) soft deletion failed.");
                                DB::rollBack();
                            }
                        } catch (\Exception $e) {
                            DB::rollBack();
                            Log::error("Failed deactivating user ({$user->id}): " . $e->getMessage());
                        }
                    }
                }
            }
        }
    }
    
    /**
     * Check if user has additional properties with status REG_APP that have
     * applications linked with non-final status (not APP_APR or APP_REJ)
     */
    protected function hasActiveAdditionalProperties($applicantNumber)
    {
        if (empty($applicantNumber)) {
            return false;
        }
        
        $additionalProperties = \App\Models\NewlyAddedProperty::where('applicant_number', $applicantNumber)
            ->where('status', 'REG_APP')
            ->get();
        
        foreach ($additionalProperties as $property) {
            if ($this->hasNonFinalApplication($property->old_property_id)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if a property has any application with non-final status
     */
    protected function hasNonFinalApplication($oldPropertyId)
    {
        // Get all applications
        $applications = \App\Models\Application::all();
        
        foreach ($applications as $application) {
            $serviceType = $application->service_type;
            $modelId = $application->model_id;
            
            if ($serviceType) {
                $item = \App\Models\Item::find($serviceType);
                
                if ($item && $item->item_code == 'NOC') {
                    $nocApplication = \App\Models\NocApplication::find($modelId);
                    if ($nocApplication && $nocApplication->old_property_id == $oldPropertyId) {
                        if (!in_array($application->status, ['APP_APR', 'APP_REJ'])) {
                            return true;
                        }
                    }
                } elseif ($item && $item->item_code == 'SUB_MUT') {
                    $mutationApplication = \App\Models\MutationApplication::find($modelId);
                    if ($mutationApplication && $mutationApplication->old_property_id == $oldPropertyId) {
                        if (!in_array($application->status, ['APP_APR', 'APP_REJ'])) {
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
     * Statuses that are not REG_APP or REG_REJ should keep the user active
     */
    protected function hasPendingAdditionalProperties($applicantNumber)
    {
        if (empty($applicantNumber)) {
            return false;
        }
        
        return \App\Models\NewlyAddedProperty::where('applicant_number', $applicantNumber)
            ->whereNotIn('status', ['REG_APP', 'REG_REJ'])
            ->exists();
    }
    
    /**
     * Delete expired REG_APP additional properties that have no applications or have applications with final status
     */
    protected function deleteExpiredAdditionalProperties($applicantNumber, $maxInactiveDays)
    {
        $cutoffDate = Carbon::now()->subDays($maxInactiveDays);
        
        $additionalProperties = \App\Models\NewlyAddedProperty::where('applicant_number', $applicantNumber)
            ->where('status', 'REG_APP')
            ->where('updated_at', '<=', $cutoffDate)
            ->get();
        
        foreach ($additionalProperties as $property) {
            $hasFinalStatusOnly = $this->hasOnlyFinalStatusApplications($property->old_property_id);
            
            if ($hasFinalStatusOnly) {
                $property->delete();
                Log::info("Soft deleted additional property ID: {$property->id} for applicant_number: {$applicantNumber}");
            }
        }
    }
    
    /**
     * Check if property has no applications or only applications with final status (APP_APR or APP_REJ)
     */
    protected function hasOnlyFinalStatusApplications($oldPropertyId)
    {
        $hasAnyApplication = false;
        $hasNonFinalApplication = false;
        
        // Get all applications
        $applications = \App\Models\Application::all();
        
        foreach ($applications as $application) {
            $serviceType = $application->service_type;
            $modelId = $application->model_id;
            
            if ($serviceType) {
                $item = \App\Models\Item::find($serviceType);
                
                if ($item && $item->item_code == 'NOC') {
                    $nocApplication = \App\Models\NocApplication::find($modelId);
                    if ($nocApplication && $nocApplication->old_property_id == $oldPropertyId) {
                        $hasAnyApplication = true;
                        if (!in_array($application->status, ['APP_APR', 'APP_REJ'])) {
                            $hasNonFinalApplication = true;
                            break;
                        }
                    }
                } elseif ($item && $item->item_code == 'SUB_MUT') {
                    $mutationApplication = \App\Models\MutationApplication::find($modelId);
                    if ($mutationApplication && $mutationApplication->old_property_id == $oldPropertyId) {
                        $hasAnyApplication = true;
                        if (!in_array($application->status, ['APP_APR', 'APP_REJ'])) {
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

    //Deleting the temp application, coapplicant, and document with document keys before soft deleting the user
    protected function deleteUserDraftTempApplications($user)
    {
        $generalFunctions = new GeneralFunctions();

        $tempTables = [
            [
                'model' => \App\Models\TempNoc::class,
                'model_name' => 'TempNoc',
            ],
            [
                'model' => \App\Models\TempSubstitutionMutation::class,
                'model_name' => 'TempSubstitutionMutation',
            ],
            [
                'model' => \App\Models\TempConversionApplication::class,
                'model_name' => 'TempConversionApplication',
            ],
            [
                'model' => \App\Models\TempDeedOfApartment::class,
                'model_name' => 'TempDeedOfApartment',
            ],
            // [
            //     'model' => \App\Models\TempSalePermission::class,
            //     'model_name' => 'TempSalePermission',
            // ],
        ];

        foreach ($user->userProperties as $property) {
            $oldPropertyId = $property->old_property_id ?? $property->new_property_id;

            if (empty($oldPropertyId)) {
                continue;
            }

            foreach ($tempTables as $tempTable) {
                $draftRecords = $tempTable['model']::where('old_property_id', $oldPropertyId)->get();

                foreach ($draftRecords as $draftRecord) {
                    $generalFunctions->deleteApplicationAllTempData(
                        $tempTable['model_name'],
                        $draftRecord->id,
                        $draftRecord->service_type ?? null
                    );

                    Log::info("Deleted draft temp application {$tempTable['model_name']} ID: {$draftRecord->id} for old_property_id: {$oldPropertyId}");
                }
            }
        }
    }
}
