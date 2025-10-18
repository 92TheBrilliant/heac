<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Get the research associated with this tag.
     */
    public function research(): BelongsToMany
    {
        return $this->belongsToMany(Research::class, 'research_tag');
    }

    /**
     * Get the count of research with this tag.
     */
    public function getResearchCountAttribute(): int
    {
        return $this->research()->count();
    }
}
