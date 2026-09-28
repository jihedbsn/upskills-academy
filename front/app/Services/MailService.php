<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Mail\TestEmail;
use App\Mail\WelcomeEmail;
use App\Mail\OrderConfirmationEmail;
use App\Mail\CourseEnrollmentEmail;

class MailService
{
    /**
     * Envoyer un email de test
     */
    public function sendTestEmail($to, $name = 'User', $content = 'Hello World')
    {
        try {
            Mail::to($to)->send(new TestEmail($name, $content));
            return [
                'success' => true,
                'message' => 'Email de test envoyé avec succès'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'email',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Envoyer un email de bienvenue
     */
    public function sendWelcomeEmail($student)
    {
        try {
            Mail::to($student->email)->send(new WelcomeEmail($student));
            return [
                'success' => true,
                'message' => 'Email de bienvenue envoyé avec succès'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'email de bienvenue',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Envoyer un email de confirmation de commande
     */
    public function sendOrderConfirmationEmail($order, $student)
    {
        try {
            Mail::to($student->email)->send(new OrderConfirmationEmail($order, $student));
            return [
                'success' => true,
                'message' => 'Email de confirmation de commande envoyé avec succès'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'email de confirmation',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Envoyer un email de confirmation d'inscription à un cours
     */
    public function sendCourseEnrollmentEmail($course, $student)
    {
        try {
            Mail::to($student->email)->send(new CourseEnrollmentEmail($course, $student));
            return [
                'success' => true,
                'message' => 'Email d\'inscription au cours envoyé avec succès'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'email d\'inscription',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Envoyer un email personnalisé
     */
    public function sendCustomEmail($to, $subject, $view, $data = [])
    {
        try {
            Mail::send($view, $data, function ($message) use ($to, $subject) {
                $message->to($to)
                        ->subject($subject);
            });
            return [
                'success' => true,
                'message' => 'Email personnalisé envoyé avec succès'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'email personnalisé',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Vérifier la configuration mail
     */
    public function checkMailConfiguration()
    {
        $driver = config('mail.default');
        $config = config("mail.mailers.{$driver}");
        
        return [
            'driver' => $driver,
            'is_configured' => $driver !== 'log',
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
            'host' => $config['host'] ?? 'N/A',
            'port' => $config['port'] ?? 'N/A',
        ];
    }
}

