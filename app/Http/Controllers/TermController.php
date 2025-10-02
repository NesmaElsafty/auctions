<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Term;
use App\Http\Resources\TermResource;
use Exception;
use App\Services\TermService;
use App\Helpers\PaginationHelper;

class TermController extends Controller
{
    //
    protected $termService;
    public function __construct(TermService $termService)
    {
        $this->termService = $termService;
    }
    public function index(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|string|in:term,privacy,faq',
                'segment' => 'required|string|in:user,agent,admin',
            ]);
        
        $terms = $this->termService->UserIndex($request->all())->paginate(10);

        if($request->segment == 'admin') {
            $terms = $this->termService->adminIndex($request->all())->paginate(10);
            
        }

        return response()->json([
            "status" => "success",
            "message" => "Terms fetched successfully",
                "data" => TermResource::collection($terms),
                "pagination" => PaginationHelper::paginate($terms)
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to fetch terms",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $term = Term::find($id);
            if(!$term) {
                return response()->json([
                    "status" => "error",
                    "message" => "Term not found"
                ], 404);
            }
        return response()->json([
            "status" => "success",
            "message" => "Term fetched successfully",
            "data" => new TermResource($term)
        ], 200);
      
        }catch(Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to fetch term",
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
                'type' => 'required|string|in:term,privacy,faq',
                'segment' => 'required|string|in:user,agent,admin',
            ]);

            $term = $this->termService->create($request->all());

            return response()->json([
                "status" => "success",
                "message" => "Term stored successfully",
                "data" => new TermResource($term)
            ], 201);
        }catch(Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to store term",
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
                'type' => 'required|string|in:term,privacy,faq',
                'segment' => 'required|string|in:user,agent,admin',
            ]);

        $term = $this->termService->update($request->all(), $id);

        return response()->json([
            "status" => "success",
            "message" => "Term updated successfully",
            "data" => new TermResource($term)
        ], 200);
    
        }catch(Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to update term",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $term = $this->termService->destroy($id);

            return response()->json([
                "status" => "success",
                "message" => "Term deleted successfully",
            ], 200);

        }catch(Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to delete term",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function bulkActions(Request $request)
    {
        try {
            $request->validate([
                'action' => 'required|string|in:activationToggle,export,delete',
                'ids' => 'nullable|array',
                'ids.*' => 'required|string|exists:terms,id',
            ]);

            $ids = $request->ids;
            if(isset($request->type) && $request->action == 'export') {
                $ids = Term::where('type', $request->type)->get()->pluck('id');
            }
        
            switch($request->action) {
                case 'activationToggle':
                    $this->termService->activationToggle($ids);
                    break;
                case 'export':
                    $result = $this->termService->export($ids);
                    break;
                case 'delete':
                    $this->termService->delete($ids);
                    break;
            }

            return response()->json([
                "status" => "success",
                "message" => "Bulk actions performed successfully",
                "data" => $result ?? null,
            ], 200);
        }catch(Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to perform bulk actions",
                "error" => $e->getMessage()
            ], 500);
        }
    }
}
