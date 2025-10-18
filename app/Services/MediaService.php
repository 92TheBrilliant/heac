<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class MediaService
{
    /**
     * Allowed image mime types
     */
    private const ALLOWED_IMAGE_TYPES = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
    ];

    /**
     * Allowed document mime types
     */
    private const ALLOWED_DOCUMENT_TYPES = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    ];

    /**
     * Maximum file size in bytes (10MB)
     */
    private const MAX_FILE_SIZE = 10485760;

    /**
     * Thumbnail sizes
     */
    private const THUMBNAIL_SIZES = [
        'small' => [150, 150],
        'medium' => [300, 300],
        'large' => [600, 600],
    ];

    /**
     * Upload a file with validation and optimization.
     */
    public function upload(UploadedFile $file, ?string $folder = null): Media
    {
        // Validate file
        $this->validateFile($file);

        // Generate unique filename
        $fileName = $this->generateUniqueFileName($file);
        
        // Determine storage path
        $path = $folder ? "media/{$folder}/{$fileName}" : "media/{$fileName}";
        
        // Store file
        $disk = config('filesystems.default', 'public');
        Storage::disk($disk)->put($path, file_get_contents($file->getRealPath()));

        // Create media record
        $media = Media::create([
            'name' => $file->getClientOriginalName(),
            'file_name' => $fileName,
            'mime_type' => $file->getMimeType(),
            'path' => $path,
            'disk' => $disk,
            'size' => $file->getSize(),
            'folder_id' => null, // Can be set later if folder management is implemented
        ]);

        // Generate thumbnails for images
        if ($this->isImage($file)) {
            $this->generateThumbnails($media, array_keys(self::THUMBNAIL_SIZES));
            
            // Optimize image (convert to WebP if not already)
            if ($file->getMimeType() !== 'image/webp') {
                $this->optimizeImage($media);
            }
        }

        return $media;
    }

    /**
     * Generate thumbnails for images.
     */
    public function generateThumbnails(Media $media, array $sizes = []): array
    {
        if (!$media->isImage()) {
            return [];
        }

        $thumbnails = [];
        $manager = new ImageManager(new Driver());

        // Use all sizes if none specified
        if (empty($sizes)) {
            $sizes = array_keys(self::THUMBNAIL_SIZES);
        }

        foreach ($sizes as $sizeName) {
            if (!isset(self::THUMBNAIL_SIZES[$sizeName])) {
                continue;
            }

            [$width, $height] = self::THUMBNAIL_SIZES[$sizeName];

            // Get original image
            $originalPath = Storage::disk($media->disk)->path($media->path);
            $image = $manager->read($originalPath);

            // Resize image
            $image->cover($width, $height);

            // Generate thumbnail path
            $pathInfo = pathinfo($media->path);
            $thumbnailPath = $pathInfo['dirname'] . '/thumbnails/' . $sizeName . '_' . $pathInfo['basename'];

            // Save thumbnail
            $thumbnailFullPath = Storage::disk($media->disk)->path($thumbnailPath);
            
            // Create directory if it doesn't exist
            $thumbnailDir = dirname($thumbnailFullPath);
            if (!is_dir($thumbnailDir)) {
                mkdir($thumbnailDir, 0755, true);
            }

            $image->save($thumbnailFullPath);

            $thumbnails[$sizeName] = $thumbnailPath;
        }

        return $thumbnails;
    }

    /**
     * Optimize image by converting to WebP format.
     */
    public function optimizeImage(Media $media): void
    {
        if (!$media->isImage() || $media->mime_type === 'image/webp') {
            return;
        }

        $manager = new ImageManager(new Driver());

        // Get original image
        $originalPath = Storage::disk($media->disk)->path($media->path);
        $image = $manager->read($originalPath);

        // Generate WebP path
        $pathInfo = pathinfo($media->path);
        $webpPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';
        $webpFullPath = Storage::disk($media->disk)->path($webpPath);

        // Save as WebP with quality 85
        $image->toWebp(85)->save($webpFullPath);

        // Update media record to point to WebP version
        $media->update([
            'path' => $webpPath,
            'mime_type' => 'image/webp',
            'file_name' => $pathInfo['filename'] . '.webp',
        ]);

        // Optionally delete original file
        // Storage::disk($media->disk)->delete($originalPath);
    }

    /**
     * Delete media with file cleanup.
     */
    public function delete(Media $media): bool
    {
        $disk = $media->disk ?? 'public';

        // Delete main file
        if (Storage::disk($disk)->exists($media->path)) {
            Storage::disk($disk)->delete($media->path);
        }

        // Delete thumbnails if image
        if ($media->isImage()) {
            $pathInfo = pathinfo($media->path);
            $thumbnailDir = $pathInfo['dirname'] . '/thumbnails';
            
            foreach (self::THUMBNAIL_SIZES as $sizeName => $dimensions) {
                $thumbnailPath = $thumbnailDir . '/' . $sizeName . '_' . $pathInfo['basename'];
                if (Storage::disk($disk)->exists($thumbnailPath)) {
                    Storage::disk($disk)->delete($thumbnailPath);
                }
            }
        }

        // Delete media record
        return $media->delete();
    }

    /**
     * Validate uploaded file.
     */
    private function validateFile(UploadedFile $file): void
    {
        $mimeType = $file->getMimeType();
        $allowedTypes = array_merge(self::ALLOWED_IMAGE_TYPES, self::ALLOWED_DOCUMENT_TYPES);

        if (!in_array($mimeType, $allowedTypes)) {
            throw new \InvalidArgumentException("File type {$mimeType} is not allowed.");
        }

        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new \InvalidArgumentException("File size exceeds maximum allowed size of " . (self::MAX_FILE_SIZE / 1048576) . "MB.");
        }
    }

    /**
     * Generate a unique filename with sanitization.
     */
    private function generateUniqueFileName(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        // Sanitize the base name
        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        
        // Remove any potentially dangerous characters
        $baseName = preg_replace('/[^a-zA-Z0-9\-_]/', '', $baseName);
        
        // Limit length
        $baseName = substr($baseName, 0, 50);
        
        // Use slug for additional safety
        $baseName = Str::slug($baseName);
        
        // If empty after sanitization, use a default name
        if (empty($baseName)) {
            $baseName = 'file';
        }
        
        // Generate unique filename with timestamp and random string
        return $baseName . '_' . time() . '_' . Str::random(8) . '.' . $extension;
    }

    /**
     * Check if file is an image.
     */
    private function isImage(UploadedFile $file): bool
    {
        return in_array($file->getMimeType(), self::ALLOWED_IMAGE_TYPES);
    }
}
