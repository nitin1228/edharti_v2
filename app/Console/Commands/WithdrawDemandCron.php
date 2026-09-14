<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Models\ConversionApplication;
use App\Models\DeedOfApartmentApplication;
use Illuminate\Console\Command;
use App\Services\DemandService;
use App\Models\Demand;
use App\Models\DemandDetail;
use App\Models\LandUseChangeApplication;
use App\Models\MutationApplication;
use App\Models\NocApplication;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\CommonMail;
use App\Models\PropertyLeaseDetail;
use App\Models\User;
use App\Services\CommunicationService;
use Illuminate\Support\Facades\Log;

class WithdrawDemandCron extends Command
{
    protected $signature = 'demand:withdraw';
    protected $description = 'Automatically withdraws demand & cancel application after 90 days of non-payment by the applicant';

    protected $demandService;
    protected $communicationService;

    public function __construct(CommunicationService $communicationService, DemandService $demandService)
    {
        parent::__construct();
        $this->demandService = $demandService;
        $this->communicationService = $communicationService;
    }

    public function handle()
    {
        $cutoffDate     = Carbon::now()->subDays(90);
        $demandStatus   = getServiceType('DEM_PENDING');
        $appCancelStatus = getServiceType('APP_CAN');

        if (!$demandStatus) {
            $this->info("Demand status not found.");
            return Command::SUCCESS;
        }

        Demand::where('status', $demandStatus)
            ->whereNotNull('app_no')
            ->where('approved_at', '<=', $cutoffDate)
            ->chunk(100, function ($demands) use ($appCancelStatus) {
                foreach ($demands as $demand) {
                    $result = $this->demandService->withdrawDemand($demand->id, 1, true);

                    if (!$result['status']) {
                        $this->error("Demand ID {$demand->id} withdraw failed: " . $result['message']);
                        continue;
                    }

                    if (!empty($demand->app_no)) {
                        $this->cancelApplication($demand, $appCancelStatus);
                    }

                    $this->addRemark($demand->id);

                    $this->info("Demand ID {$demand->id} expired successfully.");
                }
            });

        return Command::SUCCESS;
    }

    private function cancelApplication(Demand $demand, int $appCancelStatus): void
    {
        $app = Application::where('application_no', $demand->app_no)->first();
        if (!$app || empty($app->service_type)) {
            return;
        }

        $applicationType = getServiceCodeById($app->service_type);
        $applicationClasses = [
            'SUB_MUT'    => [MutationApplication::class, 'Mutation'],
            'CONVERSION' => [ConversionApplication::class, 'Conversion'],
            'LUC'        => [LandUseChangeApplication::class, 'Land Use Change'],
            'DOA'        => [DeedOfApartmentApplication::class, 'Deed Of Apartment'],
            'NOC'        => [NocApplication::class, 'No Objection Certificate'],
        ];

        if (!isset($applicationClasses[$applicationType])) {
            return;
        }

        [$modelClass, $typeOfApplication] = $applicationClasses[$applicationType];
        $modelClass::where('application_no', $demand->app_no)->update(['status' => $appCancelStatus]);
        $app->update(['status' => $appCancelStatus]);

        $getApplicationDetails = $modelClass::where('application_no', $demand->app_no)->first();

        if ($app->created_by) {
            $this->notifyUser($app->created_by, $demand, $getApplicationDetails, $typeOfApplication);
        }
    }

    private function notifyUser(int $userId, Demand $demand, $getApplicationDetails, string $typeOfApplication): void
    {
        $user = User::find($userId);
        if (!$user) {
            return;
        }

        $propertyKnownAs = PropertyLeaseDetail::where('property_master_id', $getApplicationDetails->property_master_id)
            ->pluck('presently_known_as')
            ->first();

        $data = [
            'demand_no' => $demand->unique_id,
            'amount' => $demand->net_total,
            'application_no' => $demand->app_no,
            'application_type' => $typeOfApplication,
            'property_details' => $propertyKnownAs . " [{$getApplicationDetails->old_property_id} ({$getApplicationDetails->new_property_id})]"
        ];

        $action = 'APP_DEMAND_DACT';

        $this->sendNotifications($user, $data, $action);
    }

    private function sendNotifications(User $user, array $data, string $action): void
    {
        if ($user->email) {
            try {
                Mail::to($user->email)->send(new CommonMail($data, $action));
            } catch (\Exception $e) {
                Log::error("Failed to send demand email: " . $e->getMessage());
            }
        }

        if ($user->mobile_no) {
            foreach (['sendSmsMessage', 'sendWhatsAppMessage'] as $method) {
                try {
                    $this->communicationService->$method($data, $user->mobile_no, $action);
                } catch (\Exception $e) {
                    Log::error("Failed to send demand {$method}: " . $e->getMessage());
                }
            }
        }
    }

    private function addRemark(int $demandId): void
    {
        DemandDetail::where('demand_id', $demandId)
            ->update(['remarks' => 'Auto cancel application & withdraws demand due to non-payment']);
    }
}
