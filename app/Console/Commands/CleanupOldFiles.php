<?php

namespace App\Console\Commands;

use App\Models\ContactInquiry;
use App\Models\Media;
use App\Models\Page;
use App\Models\Research;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CleanupOldFiles extends Command
{
    protected $signature = 'cleanup:old-files 
                            {--days=90 : Number of days to keep files}
                            {--dry-run : Run without actually deleting files}';
    
    protected $description = 'Clean up unused media files, old log files, and archive old inquiries';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('Running in DRY RUN mode - no files will be deleted');
        }

        $this->info("Starting cleanup process (keeping files from last {$days} days)...");
        $this->newLine();

        // Clean up unused media files
        $this->cleanupUnusedMedia($dryRun);

        // Clean up old log files
        $this->cleanupOldLogs($days, $dryRun);

        // Archive old inquiries
        $this->archiveOldInquiries($days, $dryRun);

        $this->newLine();
        $this->info('Cleanup process completed successfully!');

        return Command::SUCCESS;
    }

    /**
     * Clean up unused media files that are not referenced anywhere.
     */
    private function cleanupUnusedMedia(bool $dryRun): void
    {
        $this->info('Checking for unused media files...');

        $unusedMedia = Media::whereDoesntHave('folder')
            ->where('created_at', '<', now()->subMonths(6))
            ->get();

        $deletedCount = 0;
        $freedSpace = 0;

        foreach ($unusedMedia as $media) {
            // Check if media is used in pages content
            $usedInPages = Page::where('content', 'like', '%' . $media->path . '%')
                ->orWhere('content', 'like', '%' . $media->file_name . '%')
                ->exists();

            // Check if media is used in research
            $usedInResearch = Research::where('thumbnail', $media->path)
                ->orWhere('file_path', $media->path)
                ->exists();

            if (!$usedInPages && !$usedInResearch) {
                $this->line("  - Unused media found: {$media->name} ({$this->formatBytes($media->size)})");
                
                if (!$dryRun) {
                    // Delete the actual file
                    if (Storage::disk($media->disk ?? 'public')->exists($media->path)) {
                        Storage::disk($media->disk ?? 'public')->delete($media->path);
                    }

                    // Delete thumbnail if exists
                    $pathInfo = pathinfo($media->path);
                    $thumbnailPath = $pathInfo['dirname'] . '/thumbnails/' . $pathInfo['basename'];
                    if (Storage::disk($media->disk ?? 'public')->exists($thumbnailPath)) {
                        Storage::disk($media->disk ?? 'public')->delete($thumbnailPath);
                    }

                    // Delete database record
                    $media->delete();
                }

                $deletedCount++;
                $freedSpace += $media->size;
            }
        }

        if ($deletedCount > 0) {
            $action = $dryRun ? 'Would delete' : 'Deleted';
            $this->info("  {$action} {$deletedCount} unused media files (freed {$this->formatBytes($freedSpace)})");
        } else {
            $this->info('  No unused media files found');
        }

        $this->newLine();
    }

    /**
     * Clean up old log files.
     */
    private function cleanupOldLogs(int $days, bool $dryRun): void
    {
        $this->info('Cleaning up old log files...');

        $logPath = storage_path('logs');
        $cutoffDate = now()->subDays($days);
        $deletedCount = 0;
        $freedSpace = 0;

        if (!File::exists($logPath)) {
            $this->warn('  Log directory does not exist');
            $this->newLine();
            return;
        }

        $logFiles = File::files($logPath);

        foreach ($logFiles as $file) {
            $fileTime = File::lastModified($file->getPathname());
            
            if ($fileTime < $cutoffDate->timestamp) {
                $fileSize = File::size($file->getPathname());
                $this->line("  - Old log file: {$file->getFilename()} ({$this->formatBytes($fileSize)})");
                
                if (!$dryRun) {
                    File::delete($file->getPathname());
                }

                $deletedCount++;
                $freedSpace += $fileSize;
            }
        }

        if ($deletedCount > 0) {
            $action = $dryRun ? 'Would delete' : 'Deleted';
            $this->info("  {$action} {$deletedCount} old log files (freed {$this->formatBytes($freedSpace)})");
        } else {
            $this->info('  No old log files found');
        }

        $this->newLine();
    }

    /**
     * Archive old resolved/closed inquiries.
     */
    private function archiveOldInquiries(int $days, bool $dryRun): void
    {
        $this->info('Archiving old inquiries...');

        $cutoffDate = now()->subDays($days);

        // Count inquiries to archive
        $oldInquiries = ContactInquiry::whereIn('status', ['resolved', 'closed'])
            ->where('updated_at', '<', $cutoffDate)
            ->get();

        if ($oldInquiries->isEmpty()) {
            $this->info('  No old inquiries to archive');
            $this->newLine();
            return;
        }

        $this->line("  Found {$oldInquiries->count()} old inquiries to archive");

        if (!$dryRun) {
            // Create archive table if it doesn't exist
            if (!DB::getSchemaBuilder()->hasTable('contact_inquiries_archive')) {
                DB::statement('CREATE TABLE contact_inquiries_archive LIKE contact_inquiries');
                $this->info('  Created archive table: contact_inquiries_archive');
            }

            // Move inquiries to archive
            foreach ($oldInquiries as $inquiry) {
                DB::table('contact_inquiries_archive')->insert([
                    'id' => $inquiry->id,
                    'name' => $inquiry->name,
                    'email' => $inquiry->email,
                    'phone' => $inquiry->phone,
                    'subject' => $inquiry->subject,
                    'message' => $inquiry->message,
                    'status' => $inquiry->status,
                    'ip_address' => $inquiry->ip_address,
                    'user_agent' => $inquiry->user_agent,
                    'responded_at' => $inquiry->responded_at,
                    'responded_by' => $inquiry->responded_by,
                    'created_at' => $inquiry->created_at,
                    'updated_at' => $inquiry->updated_at,
                ]);

                $inquiry->delete();
            }

            $this->info("  Archived {$oldInquiries->count()} old inquiries");
        } else {
            $this->info("  Would archive {$oldInquiries->count()} old inquiries");
        }

        $this->newLine();
    }

    /**
     * Format bytes to human-readable format.
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
