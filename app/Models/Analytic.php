<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Analytic extends Model
{
    /**
     * Disable updated_at timestamp
     */
    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'event_type',
        'trackable_type',
        'trackable_id',
        'url',
        'referrer',
        'user_agent',
        'ip_address',
        'user_id',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Get the trackable model (Page, Research, etc.)
     */
    public function trackable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who triggered the event
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter by event type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('event_type', $type);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope to get page views
     */
    public function scopePageViews($query)
    {
        return $query->where('event_type', 'page_view');
    }

    /**
     * Scope to get research views
     */
    public function scopeResearchViews($query)
    {
        return $query->where('event_type', 'research_view');
    }

    /**
     * Scope to get research downloads
     */
    public function scopeResearchDownloads($query)
    {
        return $query->where('event_type', 'research_download');
    }
}
