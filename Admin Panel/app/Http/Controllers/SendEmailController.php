<?php

namespace App\Http\Controllers;

use App\Mail\SetEmailData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Redirect;

class SendEmailController extends Controller
{
    public function __construct()
    {
    }

    function sendMail(Request $request)
    {
        $data = $request->all();
        $subject = $data['subject'];
        $message = base64_decode($data['message']);
        $recipients = $data['recipients'];
        
        // ✅ Récupérer les paramètres "From"
        $fromAddress = $data['from_address'] ?? config('mail.from.address');
        $fromName = $data['from_name'] ?? config('mail.from.name');
        
        // ✅ Récupérer les paramètres SMTP depuis la requête
        $smtpHost = $data['smtp_host'] ?? null;
        $smtpPort = $data['smtp_port'] ?? null;
        $smtpUsername = $data['smtp_username'] ?? null;
        $smtpPassword = $data['smtp_password'] ?? null;
        $smtpEncryption = $data['smtp_encryption'] ?? 'tls';

        Log::info('Tentative d\'envoi d\'email', [
            'subject' => $subject,
            'recipients' => $recipients,
            'from_address' => $fromAddress,
            'from_name' => $fromName,
            'smtp_host' => $smtpHost
        ]);

        try {
            // ✅ Configurer dynamiquement SMTP si les paramètres sont fournis
            if ($smtpHost && $smtpPort && $smtpUsername && $smtpPassword) {
                Config::set('mail.mailers.smtp.host', $smtpHost);
                Config::set('mail.mailers.smtp.port', $smtpPort);
                Config::set('mail.mailers.smtp.username', $smtpUsername);
                Config::set('mail.mailers.smtp.password', $smtpPassword);
                Config::set('mail.mailers.smtp.encryption', $smtpEncryption);
            }
            
            // ✅ Passer les paramètres "From" au Mailable
            Mail::to($recipients)->send(new SetEmailData($subject, $message, $fromAddress, $fromName));
            
            Log::info('Email envoyé avec succès', [
                'recipients' => $recipients,
                'subject' => $subject
            ]);

            return "email sent successfully!";
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi d\'email', [
                'error' => $e->getMessage(),
                'recipients' => $recipients,
                'subject' => $subject,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Erreur lors de l\'envoi de l\'email',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}