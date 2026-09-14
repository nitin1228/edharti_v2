<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PaymentStatusCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // This is use less, we are not using this.
    protected $signature = 'payment-status-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Payment status every day';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $payments = Payment::whereIn('status',[getStatusName('PAY_PENDING'),1])->get();
        foreach($payments as $payment){
            $paymentId = $payment->id;
             Log::info("Payment Status Check RUNNING for Payment ID: " . $paymentId ."------" .date('YmdHis'));
            checkUpdatedPaymentStatus($paymentId);
        }
    }
}
