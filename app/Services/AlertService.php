<?php

namespace App\Services;

use App\Models\Alert;

class AlertService
{
    public function myAlerts($user_id){
        $alerts = Alert::where('user_id', $user_id)->orderBy('created_at', 'desc');
        return $alerts;
    }

    public function createAlert($data, $user_id){
        $alert = Alert::create([
            'title' => $data['title'],
            'content' => $data['content'],
            'user_id' => $user_id,
        ]);

        return $alert;
    }

    public function readToggle($id){
        $alert = Alert::find($id);
        $alert->is_read = !$alert->is_read;
        $alert->save();
        return $alert;
    }

    public function deleteAlert($id){
        $alert = Alert::find($id);
        $alert->delete();
        return $alert;
    }

}