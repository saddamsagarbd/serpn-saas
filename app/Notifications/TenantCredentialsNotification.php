<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TenantCredentialsNotification extends Notification
{
    use Queueable;

    protected $tenant;
    protected $password;
    protected $loginUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct($tenant, $password, $loginUrl)
    {
        $this->tenant = $tenant;
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
        return (new MailMessage)
            ->subject('🚀 Your ERP Workspace is Ready - ' . $this->tenant->company_name)
            ->greeting('Hello ' . $this->tenant->owner_name . ',')
            ->line('Congratulations! Your isolated ERP cluster and database instance have been successfully provisioned.')
            ->line('Here are your administrative login credentials:')
            ->line('**Workspace URL:** ' . $this->loginUrl)
            ->line('**Admin Email:** ' . $this->tenant->owner_email)
            ->line('**Temporary Password:** ' . $this->password)
            ->action('Login to Workspace', $this->loginUrl)
            ->line('Please change your temporary password immediately after your first login for security purposes.')
            ->line('Thank you for choosing our SaaS platform!');
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