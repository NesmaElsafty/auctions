<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\AgencyResource;
use App\Services\AgencyService;
use Exception;

class AgencyController extends Controller
{
    public function __construct(private AgencyService $service)
    {
    }

    public function index(Request $request)
    {
        try {
            $agencies = $this->service->list();
            return response()->json([
                'data' => AgencyResource::collection($agencies),
                'message' => 'Agencies listed successfully',
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to list agencies', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'number' => ['required', 'string', 'max:255'],
                'address' => ['required', 'string', 'max:500'],
                'date' => ['required', 'string', 'max:255'],
                'documents.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'], // 10MB max
            ]);

            // upload documents
            $files = $request->file('documents');
            $agency = $this->service->create($request->all(), $files);

            // upload documents
            if (!empty($files)) {
                foreach ($files as $file) {
                    $agency->addMedia($file)
                        ->toMediaCollection('documents');
                }
            }
            return response()->json([
                'data' => new AgencyResource($agency),
                'message' => 'Agency created successfully',
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to create agency', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(int $agency)
    {
        try {
            $agency = $this->service->find($agency);
            return response()->json([
                'data' => new AgencyResource($agency),
                'message' => 'Agency fetched successfully',
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to fetch agency', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, int $agency)
    {
        try {
            $request->validate([
                'name' => ['sometimes', 'string', 'max:255'],
                'number' => ['sometimes', 'string', 'max:255'],
                'address' => ['sometimes', 'string', 'max:500'],
                'date' => ['sometimes', 'string', 'max:255'],
                'documents.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'], // 10MB max
            ]);

            $user = $request->user();
            $agency = $this->service->find($agency);
            if($user->id !== $agency->user_id) {
            return response()->json(['message' => 'You are not authorized to update this agency'], 403);
            }

            $agency = $this->service->update($agency->id, $request->all());
            
            $files = $request->file('documents');
            // upload documents
            if (!empty($files)) {
                // delete old documents
                $agency->getMedia('documents')->each(function ($media) {
                    $media->delete();
                });
                // upload new documents
                foreach ($files as $file) {
                    $agency->addMedia($file)
                        ->toMediaCollection('documents');
                }
            }
            return response()->json([
                'data' => new AgencyResource($agency),
                'message' => 'Agency updated successfully',
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to update agency', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, int $agency)
    {
        try {
            $user = $request->user();
            $agency = $this->service->find($agency);
            if($user->id !== $agency->user_id) {
            return response()->json(['message' => 'You are not authorized to delete this agency'], 403);
            }

            $this->service->delete($agency);
            return response()->json(['message' => 'Agency deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to delete agency', 'error' => $e->getMessage()], 500);
        }
    }
}
