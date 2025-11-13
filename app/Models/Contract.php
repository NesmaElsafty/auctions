<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Contract extends Model implements HasMedia
{
    use InteractsWithMedia, HasFactory;
    protected $guarded = [];
    protected $table = 'contracts';
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
