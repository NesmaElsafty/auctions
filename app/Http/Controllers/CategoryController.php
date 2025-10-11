<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use Exception;

class CategoryController extends Controller
{
    // category index service
    protected $categoryService;
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    // category index
    public function index(Request $request)
    {
        try {
            if(isset($request->segment) && $request->segment == 'admin') {
            $categories = $this->categoryService->adminIndex($request->all());
            } else {
                $categories = $this->categoryService->UserIndex($request->all());
            }
            return response()->json([
                'status' => 'success',
                'data' => CategoryResource::collection($categories),
                'message' => 'Categories listed successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to list categories',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            ]);
            $category = $this->categoryService->create($request->all());
            // upload image
            if ($request->hasFile('image')) {
                $category->addMediaFromRequest('image')
                    ->toMediaCollection('image');
            }
            return response()->json([
                'status' => 'success',
                'data' => new CategoryResource($category),
                'message' => 'Category created successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $category = Category::find($id);
           
            return response()->json([
                'status' => 'success',
                'data' => new CategoryResource($category),
                'message' => 'Category fetched successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, Category $category)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            ]);
            $category = Category::find($category);
            $category = $this->categoryService->update($request->all(), $category);
            
            if ($request->hasFile('image')) {
                $category->getMedia('image')->each(function ($media) {
                    $media->delete();
                });
                $category->addMediaFromRequest('image')
                    ->toMediaCollection('image');
            }
            return response()->json([
                'status' => 'success',
                'data' => new CategoryResource($category),
                'message' => 'Category updated successfully',
            ]);
            if ($request->hasFile('image')) {
                $category->getMedia('image')->each(function ($media) {
                    $media->delete();
                });
                $category->addMediaFromRequest('image')
                    ->toMediaCollection('image');
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Category $category)
    {
        try {
            $category = Category::find($category);
            $this->categoryService->destroy($category);
            return response()->json([
                'status' => 'success',
                'message' => 'Category deleted successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // public function bulkActions(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'action' => 'required|string|in:activationToggle,export,delete',
    //         ]);
    //         $ids = $request->ids;
    //         if(isset($request->action) && $request->action == 'export') {
    //             $ids = Category::get()->pluck('id');
    //         }
    //         switch($request->action) {
    //             case 'activationToggle':
    //                 $this->categoryService->activationToggle($ids);
    //                 break;
    //         }
    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Bulk actions performed successfully',
    //             'data' => $data ?? null,
    //         ], 200);
    //     } catch (Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Failed to perform bulk actions',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }
}
