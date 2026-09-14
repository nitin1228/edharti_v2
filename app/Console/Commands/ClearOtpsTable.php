<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Otp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ClearOtpsTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:otps';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes OTPs older than 30 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $expiryTime = $now->subMinutes(30);

        // Log start
        Log::info("Start# :- Clear OTP job started at " . $now);
        Log::info("Deleting OTPs with created_at < " . $expiryTime);

        // Delete OTPs older than 30 minutes
        $deleted = Otp::where('created_at', '<', $expiryTime)->delete();

        // Log result
        Log::info("Deleted {$deleted} expired OTP records.");
        Log::info("End# :- Clear OTP job ended at " . Carbon::now());

        $this->info("Deleted {$deleted} expired OTP records.");
    }
}
