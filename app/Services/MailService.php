<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;

class MailService
{
    public static function send(string $toEmail, string $subject, string $htmlBody, ?string $toName = null): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = config('mail.mailers.smtp.host');
            $mail->SMTPAuth   = true;
            $mail->Username   = config('mail.mailers.smtp.username');
            $mail->Password   = config('mail.mailers.smtp.password');
            $mail->SMTPSecure = config('mail.mailers.smtp.encryption', 'tls');
            $mail->Port       = config('mail.mailers.smtp.port', 587);

            $mail->setFrom(config('mail.from.address'), config('mail.from.name'));
            $mail->addAddress($toEmail, $toName ?? $toEmail);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;

            $mail->send();
            return true;
        } catch (Exception $e) {
            Log::error('MailService Error: ' . $mail->ErrorInfo);
            return false;
        }
    }
}
