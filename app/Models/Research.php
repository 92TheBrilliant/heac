<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Translatable\HasTranslations;

class Research extends Model
{
    use HasFactory, LogsActivity, HasTranslations;

    protected $table = 'research';

    protected $fillable = [
        'title',
        'slug',
        'abstract',
        'authors',
        'publication_date',
        'file_path',
        'file_type',
        'file_size',
        'thumbnail',
        'views_count',
        'downloads_count',
        'status',
        'featured',
    ];

    /**
     * The attributes that are translatable.
     */
    public $translatable = [
        'title',
        'abstract',
    ];

    protected $casts = [
        'authors' => 'array',
        'publication_date' => 'date',
        'featured' => 'boolean',
        'views_count' => 'integer',
        'downloads_count' => 'integer',
        'file_size' => 'integer',
    ];

    /**
     * Get the categories for the research.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'research_category');
    }

    /**
     * Get the tags for the research.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'research_tag');
    }

    /**
     * Increment the views count.
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Increment the downloads count.
     */
    public function incrementDownloads(): void
    {
        $this->increment('downloads_count');
    }

    /**
     * Scope a query to only include published research.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope a query to only include featured research.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    /**
     * Scope a query to order by publication date.
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('publication_date', 'desc');
    }

    /**
     * Scope a query to order by popularity (views).
     */
    public function scopePopular(Builder $query): Builder
    {
        return $query->orderBy('views_count', 'desc');
    }

    /**
     * Get the activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'slug', 'status', 'featured', 'publication_date'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
