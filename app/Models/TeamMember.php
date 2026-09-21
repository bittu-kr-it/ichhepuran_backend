<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TeamMember extends Model implements HasMedia
{
    use InteractsWithMedia;

    /** Same fixed-Select "which row does this land in" pattern as Partner::GROUPS. */
    public const GROUPS = [
        'team' => 'Team',
        'advisory_board' => 'Advisory Board',
    ];

    protected $fillable = ['name', 'role', 'bio', 'photo_alt', 'group', 'order', 'is_published'];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')->singleFile()->useDisk('public');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->orderBy('order');
    }
}
