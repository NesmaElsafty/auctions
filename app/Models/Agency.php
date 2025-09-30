<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Agency extends Model implements HasMedia
{
    protected $guarded = [];

    use InteractsWithMedia;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Register the media collections for the agency model.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('documents')
            ->useDisk('public')
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/webp'])
            // Spatie's File object exposes size in bytes via the `size` property
            ->acceptsFile(function ($file) {
                return ($file->size ?? 0) <= 10 * 1024 * 1024; // 10MB max
            });
    }
}
