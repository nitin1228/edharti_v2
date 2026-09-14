<?php

namespace App\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use App\Models\Template;
use App\Models\Application;
use App\Models\User;
use App\Models\CommunicationTracking;
use App\Services\CommunicationService;

class CommonPHPMail
{
    protected $data;
    protected $action;
    protected $communicationTrackingId;
    protected $communicationService;
    protected $mailData;
    protected $subject;

    public function __construct($data, $action, $communicationTrackingId = null,$attachment = null)
    {
        $this->data = $data;
        $this->attachment = $attachment;
        $this->action = $action;
        $this->communicationTrackingId = $communicationTrackingId;
        $this->communicationService = new CommunicationService();

        $template = Template::where('type', 'email')->where('action', $action)->where('status', 1)->first();

        if (!$template) {
            throw new \Exception("Email template for action '{$action}' not found.");
        }

        $this->mailData = $this->communicationService->createTemplate($template->template, $data);
        $this->subject = $template->subject;

        if (!is_null($communicationTrackingId)) {
            $communicationTracking = CommunicationTracking::where('id', $this->communicationTrackingId)->first();
            if ($communicationTracking) {
                $communicationTracking->message = $this->mailData;
                $communicationTracking->save();
            }
        }
    }

    public function send($to, $mailSettings)
    {
        $mail = new PHPMailer(true);
// dd($mailSettings);
        try {
            // SMTP config
            $mail->isSMTP();
            $mail->Host       = $mailSettings->host;
            $mail->Port       = $mailSettings->port;
            $mail->SMTPAuth   = true;
            $mail->Username   = $mailSettings->key ?? null;
            $mail->Password   = $mailSettings->auth_token ?? null;
            $mail->CharSet    = "UTF-8";
            $mail->SMTPDebug  = 0;    // set 0 in production

            // Sender/Recipient
            $mail->setFrom($mailSettings->key, 'eDharti 2.0');
            $mail->addAddress($to);

            // Subject & Body (render Blade)
            $mail->Subject = $this->subject;
            $mail->isHTML(true);
            $mail->Body    = View::make('emails.common_mail', [
                'data' => $this->data,
                'action' => $this->action,
                'mailData' => $this->mailData,
            ])->render();

            // Attachments
            if (!empty($this->attachment) && file_exists($this->attachment)) {
                $mail->addAttachment($this->attachment);
            }

            $mail->send();

            return [
                'success' => true,
                'message' => 'Mail sent successfully'
            ];
        } catch (Exception $e) {
            Log::error("PHPMailer Error: {$mail->ErrorInfo}");

            if (!is_null($this->communicationTrackingId)) {
                $communicationTracking = CommunicationTracking::where('id', $this->communicationTrackingId)->first();
                if ($communicationTracking) {
                    $communicationTracking->status = 0;
                    $communicationTracking->save();
                }
            }

            return false;
        }
    }
}
