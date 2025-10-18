<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Spatie\Backup\BackupDestination\BackupDestination;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatusFactory;

class BackupStatusWidget extends Widget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 5;
    
    protected function getViewData(): array
    {
        return [];
    }
    
    public function getView(): string
    {
        return 'filament.widgets.backup-status-widget';
    }

    public function getBackupDestinations(): array
    {
        $backupDestinations = [];
        
        try {
            $monitorConfig = \Spatie\Backup\Config\Config::fromArray(config('backup'))->monitoredBackups;
            $statuses = BackupDestinationStatusFactory::createForMonitorConfig($monitorConfig);
            
            foreach ($statuses as $status) {
                $destination = $status->backupDestination();
                
                $backupDestinations[] = [
                    'name' => $destination->backupName(),
                    'disk' => $destination->diskName(),
                    'reachable' => $destination->isReachable(),
                    'healthy' => $status->isHealthy(),
                    'amount' => $destination->backups()->count(),
                    'newest' => $destination->newestBackup()?->date()?->diffForHumans() ?? 'No backups',
                    'used_storage' => $this->formatBytes($destination->usedStorage()),
                    'health_checks' => [],
                ];
            }
        } catch (\Exception $e) {
            // If monitoring fails, return empty array
            return [];
        }
        
        return $backupDestinations;
    }

    public function getLatestBackups(): array
    {
        $backups = [];
        
        try {
            $disk = config('backup.backup.destination.disks')[0] ?? 'local';
            $backupName = config('backup.backup.name');
            
            $destination = BackupDestination::create($disk, $backupName);
            
            if ($destination->isReachable()) {
                foreach ($destination->backups()->take(5) as $backup) {
                    $backups[] = [
                        'path' => basename($backup->path()),
                        'date' => $backup->date()->format('Y-m-d H:i:s'),
                        'size' => $this->formatBytes($backup->size()),
                        'age' => $backup->date()->diffForHumans(),
                    ];
                }
            }
        } catch (\Exception $e) {
            // If fetching backups fails, return empty array
            return [];
        }
        
        return $backups;
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

    public function runBackup(): void
    {
        try {
            Artisan::call('backup:run', ['--only-db' => true]);
            
            $this->dispatch('backup-started', [
                'message' => 'Backup started successfully. This may take a few minutes.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('backup-failed', [
                'message' => 'Backup failed: ' . $e->getMessage(),
            ]);
        }
    }
}
