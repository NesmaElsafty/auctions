<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SubCategoryInputService;
use App\Http\Resources\SubCategoryInputResource;
use Exception;
use App\Models\SubCategoryInput;
use App\Helpers\PaginationHelper;


class SubCategoryInputController extends Controller
{
    // sub category input service
    protected $subCategoryInputService;
    public function __construct(SubCategoryInputService $subCategoryInputService)
    {
        $this->subCategoryInputService = $subCategoryInputService;
    }

    // sub category input index
    public function index(Request $request)
    {
        try {
        // use service to list sub category inputs
        $subCategoryInputs = $this->subCategoryInputService->list($request->all());
        return response()->json([
            'status' => 'success',
            'data' => SubCategoryInputResource::collection($subCategoryInputs),
                'message' => 'Sub category inputs listed successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to list sub category inputs',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // store sub category input
    public function store(Request $request)
    {
        try {
            $request->validate([
                'sub_category_id' => 'required|exists:sub_categories,id',
                'name' => 'required|string',
                'type' => 'required|string|in:text,number,select,date,radio,checkbox,textarea,file,map',
                'placeholder' => 'required|string',
            ]);
            
        $subCategoryInput = $this->subCategoryInputService->create($request->all());
        
        return response()->json([
            'status' => 'success',
            'data' => new SubCategoryInputResource($subCategoryInput),
                'message' => 'Sub category input created successfully',
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create sub category input',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // show sub category input
    public function show($id)
    {
        try {
            $subCategoryInput = SubCategoryInput::find($id);
            if(!$subCategoryInput) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sub category input not found',
                ], 404);
            }
            return response()->json([
                'status' => 'success',
                'data' => new SubCategoryInputResource($subCategoryInput),
            ], 200);
        }catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to show sub category input',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // update sub category input
    public function update(Request $request, $id)
    {
        try {
            
            $subCategoryInput = SubCategoryInput::find($id);
            if(!$subCategoryInput) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sub category input not found',
                ], 404);
            }
            $subCategoryInput = $this->subCategoryInputService->update($request->all(), $id);
            return response()->json([
                'status' => 'success',
                'data' => new SubCategoryInputResource($subCategoryInput),
                'message' => 'Sub category input updated successfully',
            ], 200);
        }catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update sub category input',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // delete sub category input
    public function destroy($id)
    {
        try {
            $subCategoryInput = SubCategoryInput::find($id);
            if(!$subCategoryInput) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sub category input not found',
                ], 404);
            }
            
            $subCategoryInput = $this->subCategoryInputService->destroy($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Sub category input deleted successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json ([
                'status' => 'error',
                'message' => 'Failed to delete sub category input',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // get sub category inputs by sub category id
    public function getSubCategoryInputsBySubCategoryId(Request $request)
    {
        try {
            $request->validate([
                'sub_category_id' => 'required|exists:sub_categories,id',
            ]);
            $subCategoryInputs = $this->subCategoryInputService->getSubCategoryInputsBySubCategoryId($request->sub_category_id);
            
            return response()->json ([
                'status' => 'success',
                'data' => SubCategoryInputResource::collection($subCategoryInputs),
                'message' => 'Sub category inputs fetched successfully',
            ], 200);
        
        } catch (Exception $e) {
            return response()->json ([
                'status' => 'error',
                'message ' => 'Failed to get sub category inputs by sub category id',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
