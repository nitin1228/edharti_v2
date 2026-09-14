<?php

namespace App\Jobs;

use App\Mail\DownloadReady;
use App\Mail\CommonPHPMail; // DONE BY SWATI MISHRA ON 02/03/2026 – MAIL SETTING
use App\Services\ReportService;
use App\Services\SettingsService; // DONE BY SWATI MISHRA ON 02/03/2026 – MAIL SETTING
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Log;

class ReportExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $filters;
    protected $email;
    public function __construct($filter, $email)
    {
        $this->filters = $filter;
        $this->email = $email;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // DONE BY SWATI MISHRA ON 02/03/2026 – ensure export directory exists
        Storage::makeDirectory('public/exports');

        $fileName = 'public/exports/' . date('YmdHis') . '.xlsx';
        $filters = $this->filters;
        // $page = 1;
        $service = new ReportService();
        $rows = [];
        $results = $service->filterResults($filters, false);

        foreach ($results as $index => $item) {
            $rows[] = [
                'old property id' => $item->old_propert_id,
                'unique property id' => $item->unique_propert_id,
                'land type' => $item->land_type,
                'status' => $item->status,
                'lease tenure' => $item->lease_tenure,
                'land use' => $item->land_use,
                'area' => $item->area_in_sqm,
                'address' => $item->address,
                'lesse name' => $item->lesse_name,
                'gr in re rs' => $item->gr_in_re_rs,
                'gr' => $item->gr,
            ];
        }
        // } while ($page < 3); //while ($page < 3); while (count($results) == $chunkSize);
        // done by Swati Mishra on 02-03-2026 start
        if (!empty($rows)) {

            // Create Excel file
            (new FastExcel($rows))->export(Storage::path($fileName));

            if (!is_null($this->email)) {

                
                // use template based CommonPHPMail instead of Laravel Mail
                $action = 'REPORT_EXPORT';

                $downloadLink = url('/download/' . base64_encode($fileName));
                $clickableLink = '<a href="' . $downloadLink . '" target="_blank">Click Here</a>';

                $data = [
                    'link' => $clickableLink,
                    'file_name' => basename($fileName),
                ];

                $checkEmailTemplateExists = checkTemplateExists('email', $action);

                if (!empty($checkEmailTemplateExists)) {
                    try {
                        $mailSettings = app(SettingsService::class)->getMailSettings($action);

               
                        // attach generated excel file
                        $attachment = Storage::path($fileName);

                        $mailer = new CommonPHPMail($data, $action, null, $attachment);
                        $mailer->send($this->email, $mailSettings);

                        Log::info('ReportExport: Email sent successfully with attachment.', [
                            'email' => $this->email,
                            'file'  => $fileName,
                        ]);
                    } catch (\Exception $e) {
                        Log::error('ReportExport: Email sending failed.', [
                            'email' => $this->email,
                            'error' => $e->getMessage(),
                        ]);
                    }
                } else {
                    Log::warning('ReportExport: Email template REPORT_EXPORT not found.');
                }
            }
	   // done by Swati Mishra on 02-03-2026 end
        }
    }
}
