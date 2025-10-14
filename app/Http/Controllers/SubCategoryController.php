<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubCategory;
use App\Http\Resources\SubCategoryResource;
use App\Services\SubCategoryService;
use Exception;

class SubCategoryController extends Controller
{
    // sub category index service
    protected $subCategoryService;
    public function __construct(SubCategoryService $subCategoryService)
    {
        $this->subCategoryService = $subCategoryService;
    }

    // category index
    public function index(Request $request)
    {
        try {
            
            $subCategories = $this->subCategoryService->list($request->all());
            
            return response()->json([
                'status' => 'success',
                    'data' => SubCategoryResource::collection($subCategories),
                'message' => 'Sub Categories listed successfully',
            ], 200);
        
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to list subcategories',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getSubCategoriesByCategoryId(Request $request)
    {
        try {
            $request->validate([
                'category_id' => 'required|exists:categories,id',
            ]);
            $subCategories = $this->subCategoryService->getSubCategoriesByCategoryId($request->category_id);
            
            return response()->json([
                'status' => 'success',
                'data' => SubCategoryResource::collection($subCategories),
                'message' => 'Sub categories fetched successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get sub categories by category id',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'category_id' => 'required|exists:categories,id',
            ]);
            $category = $this->subCategoryService->create($request->all());
            
            return response()->json([
                'status' => 'success',
                'data' => new SubCategoryResource($category),
                'message' => 'Category created successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create sub category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $subCategory = SubCategory::find($id);

            if(!$subCategory) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sub category not found',
                ], 404);
            }
           
            return response()->json([
                'status' => 'success',
                'data' => new SubCategoryResource($subCategory),
                'message' => 'Sub category fetched successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch sub category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $subCategory = SubCategory::find($id);
            if(!$subCategory) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sub category not found',
                ], 404);
            }
            $subCategory = $this->subCategoryService->update($request->all(), $id);
            
            return response()->json([
                'status' => 'success',
                'data' => new SubCategoryResource($subCategory),
                'message' => 'Sub category updated successfully',
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update sub category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $subCategory = SubCategory::find($id);
            if(!$subCategory) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sub category not found',
                ], 404);
            }
            $this->subCategoryService->destroy($id);
            return response()->json([
                'status' => 'success',
                'message' => 'Sub category deleted successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete sub category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
