<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubCategoryInput extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function selectableData()
    {
        return $this->hasMany(SelectableData::class);
    }
}
