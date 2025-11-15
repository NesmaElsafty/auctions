<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoryTerm;
use App\Http\Resources\CategoryTermResource;
use Exception;

class CategoryTermController extends Controller
{
    //
    public function index(Request $request)
    {
        try {
        $categoryTerms = CategoryTerm::where('category_id', $request->category_id)->get();
            return response()->json([
                'message' => 'Category terms retrieved successfully',
                'data' => CategoryTermResource::collection($categoryTerms),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve category terms',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $categoryTerm = CategoryTerm::find($id);
            return response()->json([
                'message' => 'Category term retrieved successfully',
                'data' => $categoryTerm->toResource(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve category term',
                'error' => $e->getMessage(),
            ], 500);
        }
    }   

    public function store(Request $request)
    {   
        try {
        $categoryTerm = CategoryTerm::create($request->all());
        return response()->json([
            'message' => 'Category term created successfully',
            'data' => new CategoryTermResource($categoryTerm),
        ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to create category term',
                'error' => $e->getMessage(),
            ], 500);
        }
    }   

    public function update(Request $request, $id)
    {
        try {
        $categoryTerm = CategoryTerm::find($id);
        $categoryTerm->update($request->all());
        return response()->json([
            'message' => 'Category term updated successfully',
            'data' => new CategoryTermResource($categoryTerm),
        ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to update category term',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
        $categoryTerm = CategoryTerm::find($id);
        $categoryTerm->delete();
        return response()->json([
            'message' => 'Category term deleted successfully',
        ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to delete category term',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function bulkActions(Request $request)
    {
        try {
        $categoryTerms = CategoryTerm::whereIn('id', $request->ids)->get();
        foreach($categoryTerms as $categoryTerm) {
            $categoryTerm->update($request->all());
        }
        return response()->json([
            'message' => 'Category terms bulk actions updated successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to perform bulk actions',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
        $categoryTerms = CategoryTerm::whereIn('id', $request->ids)->delete();
        return response()->json([
            'message' => 'Category terms bulk deleted successfully',
        ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to bulk delete category terms',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}