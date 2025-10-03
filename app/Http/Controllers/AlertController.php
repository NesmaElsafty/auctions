<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AlertService;
use App\Http\Resources\AlertResource;
use App\Helpers\PaginationHelper;
use Exception;
use App\Models\Alert;

class AlertController extends Controller
{
    //

    protected $alertService;
    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            $alerts = $this->alertService->myAlerts($user->id)->paginate(10);
        return response()->json([
            "status" => "success",
            "data" => AlertResource::collection($alerts),
            "pagination" => PaginationHelper::paginate($alerts)
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to list alerts",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
        $user = auth()->user();
        $alert = Alert::find($id);
        if(!$alert) {
            return response()->json([
                "status" => "error",
                "message" => "Alert not found"
            ], 404);
        }

        if($alert->user_id != $user->id) {
            return response()->json([
                "status" => "error",
                "message" => "You are not authorized to show this alert"
            ], 403);
        }
        return response()->json([
            "status" => "success",
            "data" => new AlertResource($alert)
        ], 200);
        } catch (Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to show alert",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function readToggle($id)
    {
        try {
        $alert = $this->alertService->readToggle($id);
        if(!$alert) {
            return response()->json([
                "status" => "error",
                "message" => "Alert not found"
            ], 404);
        }
        return response()->json([
            "status" => "success",
            "data" => new AlertResource($alert)
        ], 200);
        } catch (Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to toggle read alert",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
        $alert = $this->alertService->deleteAlert($id);
        if(!$alert) {
            return response()->json([
                "status" => "error",
                "message" => "Alert not found"
            ], 404);
        }
        return response()->json([
            "status" => "success",
            "data" => new AlertResource($alert)
        ], 200);
        } catch (Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to destroy alert",
                "error" => $e->getMessage()
            ], 500);
        }
    }
    
}
