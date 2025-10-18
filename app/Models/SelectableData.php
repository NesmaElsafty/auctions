<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SelectableData extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function subCategoryInput()
    {
        return $this->belongsTo(SubCategoryInput::class);
    }
}
