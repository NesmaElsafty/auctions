<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    //
    protected $guarded = [];
    public function users()
    {
        return $this->belongsToMany(User::class, 'notification_users', 'notification_id', 'user_id');
    }
}
