<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryTerm extends Model
{
    //
    protected $guarded = [];
    protected $table = 'category_terms';

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
