<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Services\AlertService;
use Illuminate\Http\Request;
use Exception;

class NotificationService
{
    protected $alertService;
    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    public function index($filters)
    {
        $query = Notification::query();

        // Apply filters if provided
        // if (isset($filters['type'])) {
        //     $query->where('type', $filters['type']);
        // }

        // if (isset($filters['segment'])) {
        //     $query->where('segment', $filters['segment']);
        // }

        // if (isset($filters['search'])) {
        //     $query->where(function ($q) use ($filters) {
        //         $q->where('title', 'like', '%' . $filters['search'] . '%')
        //           ->orWhere('content', 'like', '%' . $filters['search'] . '%');
        //     });
        // }

        return $query->orderBy('created_at', 'desc');
    }

    public function show($id)
    {
        return Notification::find($id);
    }

    public function store($data)
    {
        $notification = Notification::create($data);

        $users = User::where('type', $data['segment'])->pluck('id');
        $notification->users()->attach($users);

        $alerts = [];
        if($data['status'] == 'sent') {
            foreach($users as $user) {
                $data = [
                    'title' => $data['title'],
                    'content' => $data['content'],
                ];
                $this->alertService->createAlert($data, $user);
            }
        }

        return $notification;
    }

    public function update($data, $id)
    {
        $notification = Notification::find($id);
        $notification->title = $data['title'] ?? $notification->title;
        $notification->content = $data['content'] ?? $notification->content;
        $notification->type = $data['type'] ?? $notification->type;
        $notification->segment = $data['segment'] ?? $notification->segment;
        $notification->status = $data['status'] ?? $notification->status;
        $notification->save();

        $users = User::where('type', $data['segment'])->pluck('id');
        $notification->users()->sync($users);

        if($data['type'] == 'sent') {
            foreach($users as $user) {
                $this->alertService->createAlert($data, $user);
            }
        }

        return $notification;
    }

    public function destroy($id)
    {
        $notification = Notification::find($id);
        $notification->delete();
        return $notification;
    }

  
}
