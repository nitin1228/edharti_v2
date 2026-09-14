<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Console\Command;

class checkPendingPaymentStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // This is main cron to update demand & payment as well 
    protected $signature = 'app:check-pending-payment-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command checks and update pending payment status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pendingPayments = Payment::whereNotIn('status', [getServiceType('PAY_SUCCESS'), getServiceType('PAY_FAILED')])->get();
        $paymentService = new PaymentService();
        foreach ($pendingPayments as $key => $payment) {
          //  if($payment->id == 743){
                $paymentService->checkUpdatedPaymentStatus($payment->id);
          //  }
        }
        // $paymentService = new PaymentService();
        // $paymentService->checkUpdatedPaymentStatus(743);
        // $paymentService->checkUpdatedPaymentStatus(848);
    }
}
