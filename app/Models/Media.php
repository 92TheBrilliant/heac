<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Media extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'media';

    protected $fillable = [
        'name',
        'file_name',
        'mime_type',
        'path',
        'disk',
        'size',
        'alt_text',
        'title',
        'caption',
        'folder_id',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    protected $appends = [
        'url',
        'thumbnail_url',
    ];

    /**
     * Get the folder that owns the media.
     */
    public function folder(): BelongsTo
    {
        return $this->belongsTo(MediaFolder::class, 'folder_id');
    }

    /**
     * Get the URL attribute.
     */
    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn () => Storage::disk($this->disk ?? 'public')->url($this->path)
        );
    }

    /**
     * Get the thumbnail URL attribute.
     */
    protected function thumbnailUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                // Check if this is an image
                if (!str_starts_with($this->mime_type, 'image/')) {
                    return null;
                }

                // Generate thumbnail path
                $pathInfo = pathinfo($this->path);
                $thumbnailPath = $pathInfo['dirname'] . '/thumbnails/' . $pathInfo['basename'];

                // Check if thumbnail exists
                if (Storage::disk($this->disk ?? 'public')->exists($thumbnailPath)) {
                    return Storage::disk($this->disk ?? 'public')->url($thumbnailPath);
                }

                // Return original URL if no thumbnail
                return $this->url;
            }
        );
    }

    /**
     * Check if the media is an image.
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Check if the media is a document.
     */
    public function isDocument(): bool
    {
        return in_array($this->mime_type, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Get the activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'file_name', 'mime_type'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
