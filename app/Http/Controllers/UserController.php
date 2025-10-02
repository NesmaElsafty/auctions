<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use App\Helpers\PaginationHelper;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function __construct(private UserService $userService)
    {
    }

    public function index(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:user,agent,admin',
        ]);

        $data = $this->userService->users($request->all())->paginate(10);
        $stats = $this->userService->stats($request->all());
        
        return response()->json([
            "status" => "success",
            "data" => UserResource::collection($data),
            "pagination" => PaginationHelper::paginate($data),
            "stats" => $stats
        ], 200);      
    }
}
