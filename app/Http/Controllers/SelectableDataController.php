<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SelectableData;
use App\Http\Resources\SelectableDataResource;
use App\Services\SelectableDataService;
use Exception;

class SelectableDataController extends Controller
{
    protected $selectableDataService;

    public function __construct(SelectableDataService $selectableDataService)
    {
        $this->selectableDataService = $selectableDataService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $selectableData = $this->selectableDataService->list($request->all());
            
            return response()->json([
                'status' => 'success',
                'data' => SelectableDataResource::collection($selectableData),
                'message' => 'Selectable data listed successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to list selectable data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'input_id' => 'required|exists:sub_category_inputs,id',
                'value' => 'required|string|max:255',
                'label' => 'required|string|max:255',
            ]);

            $selectableData = $this->selectableDataService->create($request->all());

            return response()->json([
                'status' => 'success',
                'data' => new SelectableDataResource($selectableData),
                'message' => 'Selectable data created successfully',
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create selectable data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $selectableData = SelectableData::with('subCategoryInput')->find($id);
            
            if (!$selectableData) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Selectable data not found',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => new SelectableDataResource($selectableData),
                'message' => 'Selectable data fetched successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch selectable data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $selectableData = SelectableData::find($id);
            if(!$selectableData) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Selectable data not found',
                ], 404);
            }
            $request->validate([
                'input_id' => 'sometimes|exists:sub_category_inputs,id',
                'value' => 'sometimes|string|max:255',
                'label' => 'sometimes|string|max:255',
                'is_active' => 'sometimes|boolean',
            ]);

            $selectableData = $this->selectableDataService->update($request->all(), $id);

            return response()->json([
                'status' => 'success',
                'data' => new SelectableDataResource($selectableData),
                'message' => 'Selectable data updated successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update selectable data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SelectableData $selectableData)
    {
        try {
            $this->selectableDataService->destroy($selectableData->id);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Selectable data deleted successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete selectable data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get input options by input ID
     */
    public function getInputOptions(Request $request)
    {
        try {
            $request->validate([
                'input_id' => 'required|exists:sub_category_inputs,id',
            ]);

            $inputOptions = $this->selectableDataService->getInputOptions($request->input_id);

            return response()->json([
                'status' => 'success',
                'data' => SelectableDataResource::collection($inputOptions),
                'message' => 'Input options fetched successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch input options',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
