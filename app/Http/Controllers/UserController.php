<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use App\Helpers\PaginationHelper;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;

class UserController extends Controller
{
    public function __construct(private UserService $userService)
    {
    }

    public function index(Request $request)
    {
        try {

            $request->validate([
                'type' => 'required|string|in:user,agent,admin',
            ]);

            $user = auth()->user();
            if($user->type != 'admin') {
                return response()->json([
                    "status" => "error",
                    "message" => "You are not authorized to list users"
                ], 403);
            }


            $data = $this->userService->users($request->all())->paginate(10);
            $stats = $this->userService->stats($request->all());
            
            return response()->json([
                "status" => "success",
                "data" => UserResource::collection($data),
                "pagination" => PaginationHelper::paginate($data),
                "stats" => $stats
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to list users",
                "error" => $e->getMessage()
            ], 500);
        }
             
    }

    public function show($id)
    {
        try {
            $user = auth()->user();
            if($user->type != 'admin') {
                return response()->json([
                    "status" => "error",
                    "message" => "You are not authorized to show user"
                ], 403);
            }
            $user = User::find($id)->load('bank_account', 'agencies');
        if (!$user) {
            return response()->json([
                "status" => "error",
                "message" => "User not found"
            ], 404);
        }
            return response()->json([
                "status" => "success",
                "data" => new UserResource($user)
            ], 200);        
        } catch (Exception $e) {
                return response()->json([
                    "status" => "error",
                    "message" => "Failed to show user",
                    "error" => $e->getMessage()
                ], 500);
            }
    }
}
