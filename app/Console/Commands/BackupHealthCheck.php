<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use App\Notifications\BackupNotification;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatusFactory;

class BackupHealthCheck extends Command
{
    protected $signature = 'backup:health-check';

    protected $description = 'Check the health of all backup destinations';

    public function handle(): int
    {
        $this->info('Checking backup health...');

        try {
            $monitorConfig = \Spatie\Backup\Config\Config::fromArray(config('backup'))->monitoredBackups;
            $statuses = BackupDestinationStatusFactory::createForMonitorConfig($monitorConfig);

            $hasUnhealthy = false;
            $details = [];

            foreach ($statuses as $status) {
                $destination = $status->backupDestination();
                $name = $destination->backupName();
                $disk = $destination->diskName();

                if (!$destination->isReachable()) {
                    $this->error("✗ {$name} ({$disk}): Unreachable");
                    $hasUnhealthy = true;
                    $details[] = "{$name} on {$disk} is unreachable";
                    continue;
                }

                if (!$status->isHealthy()) {
                    $this->warn("⚠ {$name} ({$disk}): Unhealthy");
                    $hasUnhealthy = true;
                    $details[] = "{$name} on {$disk} is unhealthy";
                } else {
                    $this->info("✓ {$name} ({$disk}): Healthy");
                }

                // Display backup info
                $backupCount = $destination->backups()->count();
                $newestBackup = $destination->newestBackup();
                $usedStorage = $this->formatBytes($destination->usedStorage());

                $this->line("  Backups: {$backupCount}");
                $this->line("  Newest: " . ($newestBackup ? $newestBackup->date()->diffForHumans() : 'None'));
                $this->line("  Storage: {$usedStorage}");
                $this->newLine();
            }

            if ($hasUnhealthy) {
                $this->error('Some backup destinations are unhealthy!');
                
                // Send notification
                $this->sendNotification('warning', 'Backup health check found issues', $details);
                
                return self::FAILURE;
            }

            $this->info('All backup destinations are healthy!');
            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Failed to check backup health: ' . $e->getMessage());
            
            $this->sendNotification('error', 'Backup health check failed', [
                'Error' => $e->getMessage()
            ]);
            
            return self::FAILURE;
        }
    }

    protected function formatBytes(int $bytes): string
    {
        if ($bytes === 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = floor(log($bytes, 1024));

        return round($bytes / pow(1024, $power), 2) . ' ' . $units[$power];
    }

    protected function sendNotification(string $type, string $message, array $details): void
    {
        $email = config('backup.notifications.mail.to');
        
        if ($email && $email !== 'your@example.com') {
            try {
                Notification::route('mail', $email)
                    ->notify(new BackupNotification($type, $message, $details));
            } catch (\Exception $e) {
                $this->warn('Failed to send notification: ' . $e->getMessage());
            }
        }
    }
}
