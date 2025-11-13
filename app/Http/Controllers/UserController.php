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
                'type' => 'required|string|in:user,admin',
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

    // bulk actions
    public function bulkActions(Request $request)
    {
        try {
            $request->validate([
                'action' => 'required|string|in:block,unblock,activationToggle,export',
                'ids' => 'nullable|array',
                'ids.*' => 'required|string|exists:users,id',
            ]);

            $user = auth()->user();
            if($user->type != 'admin') {
                return response()->json([
                    "status" => "error",
                    "message" => "You are not authorized to bulk actions"
                ], 403);
            }

            $ids = $request->ids;
            if(isset($request->type) && $request->action == 'export') {
                $ids = User::where('type', $request->type)->get()->pluck('id');
            }

            switch($request->action) {
                case 'block':
                    $this->userService->block($ids);
                    break;
                case 'unblock':
                    $this->userService->unblock($ids);
                    break;
                case 'activationToggle':
                    $this->userService->activationToggle($ids);
                    break;
                case 'export':
                    $data = $this->userService->export($ids);
                    break;
            }

            return response()->json([
                "status" => "success",
                "message" => "Bulk actions performed successfully",
                "data" => $data ?? null,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to perform bulk actions",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    // block list
    public function blockList(Request $request)
    {
        try {
            $users = User::query();
            $users = $users->onlyTrashed();
            if(isset($request->search)) {
                $users = $users->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('national_id', 'like', '%' . $request->search . '%')
                ->orWhere('phone', 'like', '%' . $request->search . '%')
                ->orWhere('address', 'like', '%' . $request->search . '%');
            }
            $users = $users->paginate(10);
            
            return response()->json([
                "status" => "success",
                "data" => UserResource::collection($users),
                "pagination" => PaginationHelper::paginate($users)
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to get block list",
                "error" => $e->getMessage()
            ], 500);
        }
    }
}
