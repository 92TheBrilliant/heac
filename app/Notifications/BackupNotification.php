<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BackupNotification extends Notification
{
    use Queueable;

    protected string $type;
    protected string $message;
    protected ?array $details;

    public function __construct(string $type, string $message, ?array $details = null)
    {
        $this->type = $type;
        $this->message = $message;
        $this->details = $details;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->getSubject())
            ->greeting($this->getGreeting())
            ->line($this->message);

        if ($this->details) {
            foreach ($this->details as $key => $value) {
                $mail->line("**{$key}:** {$value}");
            }
        }

        if ($this->type === 'success') {
            $mail->success();
        } elseif ($this->type === 'error') {
            $mail->error();
        }

        return $mail->line('Thank you for using HEAC CMS!');
    }

    protected function getSubject(): string
    {
        return match ($this->type) {
            'success' => 'Backup Completed Successfully',
            'error' => 'Backup Failed',
            'warning' => 'Backup Warning',
            default => 'Backup Notification',
        };
    }

    protected function getGreeting(): string
    {
        return match ($this->type) {
            'success' => 'Backup Successful!',
            'error' => 'Backup Failed!',
            'warning' => 'Backup Warning!',
            default => 'Backup Notification',
        };
    }
}
