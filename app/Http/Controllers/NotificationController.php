<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Http\Resources\NotificationResource;
use App\Services\NotificationService;
use App\Helpers\PaginationHelper;
use Exception;

class NotificationController extends Controller
{
    //
    protected $notificationService;
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        try {
                $user = auth()->user();
                $notifications = $this->notificationService->index($request->all())->paginate(10);
                
                return response()->json([
                    "status" => "success",
                    "data" => NotificationResource::collection($notifications),
                    "pagination" => PaginationHelper::paginate($notifications)
                ], 200);
                
        } catch (Exception $e) {
                return response()->json([
                    "status" => "error",
                    "message" => "Failed to list notifications",
                    "error" => $e->getMessage()
                ], 500);
            }
    
    }

    public function show($id)
    {
        try {

            $notification = $this->notificationService->show($id);
            if(!$notification) {
                return response()->json([
                    "status" => "error",
                    "message" => "Notification not found"
                ], 404);
            }
            return response()->json([
                "status" => "success",
                "data" => new NotificationResource($notification)
                ], 200);

        } catch (Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to show notification",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string',
                'content' => 'required|string',
                'type' => 'required|string|in:notification,alert',
                'segment' => 'required|string|in:user,agent,admin',
            ]);

            $notification = $this->notificationService->store($request->all());
           
            return response()->json([
            "status" => "success",
            "data" => new NotificationResource($notification)
        ], 201);
        } catch (Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to create notification",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string',
                'content' => 'required|string',
                'type' => 'required|string|in:notification,alert',
                'segment' => 'required|string|in:user,agent,admin',
            ]);

            $notification = $this->notificationService->update($request->all(), $id);
        return response()->json([
            "status" => "success",
            "data" => new NotificationResource($notification)
        ], 200);
        } catch (Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to update notification",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $notification = $this->notificationService->destroy($id);
            return response()->json([
                "status" => "success",
                "message" => "Notification deleted successfully",
                "data" => new NotificationResource($notification)
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to delete notification",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    
}
