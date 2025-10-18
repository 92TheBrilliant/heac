<?php

namespace App\Services;

use App\Notifications\BackupNotification;
use Illuminate\Notifications\Notifiable as NotifiableTrait;
use Spatie\Backup\Notifications\Notifiable;

class BackupNotifiable extends Notifiable
{
    use NotifiableTrait;

    public function routeNotificationForMail(): string
    {
        return config('backup.notifications.mail.to');
    }

    public function getKey(): int
    {
        return 1;
    }
}
