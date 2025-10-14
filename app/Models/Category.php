<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Category extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia; 
    protected $guarded = [];
    protected $table = 'categories';

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
    }
}
