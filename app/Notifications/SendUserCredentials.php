<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SendUserCredentials extends Notification
{
    use Queueable;

    protected $user;
    protected $password;
    protected $loginUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct($user, $password, $loginUrl)
    {
        $this->user = $user;
        $this->password = $password;
        $this->loginUrl = $loginUrl;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        try {

            $user = $this->user;
            $password = $this->password;
            $loginUrl = $this->loginUrl;
            
            $subject =  "User Credentials";

            $ccEmails = [];
            
            return (new MailMessage)
                    ->subject($subject)
                    ->from('nhd@navanarealestate.com', 'Navana Help Desk')
                    ->view('tenant.email.send-credentials', compact('user', 'subject', 'password', 'loginUrl'))
                    ->cc($ccEmails);
        }catch (\Throwable $th) {
            Log::info("User Credentials: ".$th->getMessage());
            throw $th;
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}