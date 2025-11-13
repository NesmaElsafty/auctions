<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Exception;

class ContractService
{
    /**
     * Get all contracts with optional filtering and pagination
     */
    public function getAllContracts(Request $request)
    {
        try {
            $query = Contract::with(['category', 'media']);

            // Apply filters
            if ($request->has('category_id') && $request->category_id) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('contract_number', 'like', "%{$search}%");
                });
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Apply pagination
            if ($request->has('per_page')) {
                $perPage = min($request->per_page, 100); // Limit to 100 per page
                return $query->paginate($perPage);
            }

            return $query->get();
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve contracts: " . $e->getMessage());
        }
    }

    /**
     * Create a new contract
     */
    public function createContract($data)
    {
            $contract = new Contract(); 
            $contract->title = $data['title'];
            $contract->description = $data['description'];
            $contract->category_id = $data['category_id'];
            $contract->file_path = $data['directory'] . '/' . $data['file']->getClientOriginalName();
            $contract->is_active = $data['is_active'] ?? 0;
            $contract->save();
            return $contract;
    }

    /**
     * Update an existing contract
     */
    public function updateContract(Request $request, $id)
    {
        try {
            $contract = Contract::find($id);
            if (!$contract) {
                throw new Exception("Contract not found");
            }

            // Validate the request
            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'contract_number' => 'sometimes|required|string|max:100|unique:contracts,contract_number,' . $id,
                'category_id' => 'sometimes|required|exists:categories,id',
                'start_date' => 'sometimes|required|date',
                'end_date' => 'nullable|date|after:start_date',
                'amount' => 'nullable|numeric|min:0',
                'status' => 'nullable|in:draft,active,expired,terminated',
                'terms' => 'nullable|string',
                'notes' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                throw new Exception("Validation failed: " . implode(', ', $validator->errors()->all()));
            }

            // Check if category exists (if being updated)
            if ($request->has('category_id')) {
                $category = Category::find($request->category_id);
                if (!$category) {
                    throw new Exception("Category not found");
                }
            }

            // Update the contract
            $updateData = $request->only([
                'title', 'description', 'contract_number', 'category_id',
                'start_date', 'end_date', 'amount', 'status', 'terms', 'notes'
            ]);

            $contract->update($updateData);

            // Handle file uploads if any
            if ($request->hasFile('attachments')) {
                $this->handleFileUploads($contract, $request->file('attachments'));
            }

            return $contract->load(['category', 'media']);
        } catch (Exception $e) {
            throw new Exception("Failed to update contract: " . $e->getMessage());
        }
    }

    /**
     * Delete a contract
     */
    public function deleteContract($id)
    {
        try {
            $contract = Contract::find($id);
            if (!$contract) {
                throw new Exception("Contract not found");
            }

            // Delete associated media files
            $contract->clearMediaCollection('attachments');

            // Delete the contract
            $contract->delete();

            return true;
        } catch (Exception $e) {
            throw new Exception("Failed to delete contract: " . $e->getMessage());
        }
    }

    /**
     * Get contracts by category
     */
    public function getContractsByCategory($categoryId)
    {
        try {
            $category = Category::find($categoryId);
            if (!$category) {
                throw new Exception("Category not found");
            }

            return Contract::with(['category', 'media'])
                ->where('category_id', $categoryId)
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve contracts by category: " . $e->getMessage());
        }
    }

    /**
     * Get contracts by status
     */
    public function getContractsByStatus($status)
    {
        try {
            $validStatuses = ['draft', 'active', 'expired', 'terminated'];
            if (!in_array($status, $validStatuses)) {
                throw new Exception("Invalid status. Valid statuses are: " . implode(', ', $validStatuses));
            }

            return Contract::with(['category', 'media'])
                ->where('status', $status)
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve contracts by status: " . $e->getMessage());
        }
    }

    /**
     * Get expiring contracts (within specified days)
     */
    public function getExpiringContracts($days = 30)
    {
        try {
            $expiryDate = now()->addDays($days);

            return Contract::with(['category', 'media'])
                ->where('status', 'active')
                ->where('end_date', '<=', $expiryDate)
                ->where('end_date', '>=', now())
                ->orderBy('end_date', 'asc')
                ->get();
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve expiring contracts: " . $e->getMessage());
        }
    }

    /**
     * Update contract status
     */
    public function updateContractStatus($id, $status)
    {
        try {
            $validStatuses = ['draft', 'active', 'expired', 'terminated'];
            if (!in_array($status, $validStatuses)) {
                throw new Exception("Invalid status. Valid statuses are: " . implode(', ', $validStatuses));
            }

            $contract = Contract::find($id);
            if (!$contract) {
                throw new Exception("Contract not found");
            }

            $contract->update(['status' => $status]);

            return $contract->load(['category', 'media']);
        } catch (Exception $e) {
            throw new Exception("Failed to update contract status: " . $e->getMessage());
        }
    }

    /**
     * Get contract statistics
     */
    public function getContractStatistics()
    {
        try {
            $stats = [
                'total' => Contract::count(),
                'draft' => Contract::where('status', 'draft')->count(),
                'active' => Contract::where('status', 'active')->count(),
                'expired' => Contract::where('status', 'expired')->count(),
                'terminated' => Contract::where('status', 'terminated')->count(),
                'expiring_soon' => $this->getExpiringContracts(30)->count(),
                'total_value' => Contract::where('status', 'active')->sum('amount'),
                'by_category' => Contract::select('category_id')
                    ->with('category:id,name')
                    ->selectRaw('count(*) as count')
                    ->groupBy('category_id')
                    ->get()
            ];

            return $stats;
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve contract statistics: " . $e->getMessage());
        }
    }

    /**
     * Handle file uploads for contracts
     */
    private function handleFileUploads($contract, $files)
    {
        try {
            if (is_array($files)) {
                foreach ($files as $file) {
                    $contract->addMediaFromRequest($file)
                        ->toMediaCollection('attachments');
                }
            } else {
                $contract->addMediaFromRequest($files)
                    ->toMediaCollection('attachments');
            }
        } catch (Exception $e) {
            throw new Exception("Failed to upload files: " . $e->getMessage());
        }
    }

    /**
     * Bulk operations on contracts
     */
    public function bulkOperations(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'action' => 'required|in:delete,update_status',
                'contract_ids' => 'required|array|min:1',
                'contract_ids.*' => 'required|integer|exists:contracts,id',
                'status' => 'required_if:action,update_status|in:draft,active,expired,terminated'
            ]);

            if ($validator->fails()) {
                throw new Exception("Validation failed: " . implode(', ', $validator->errors()->all()));
            }

            $action = $request->action;
            $contractIds = $request->contract_ids;
            $results = [];

            DB::beginTransaction();

            try {
                foreach ($contractIds as $contractId) {
                    $contract = Contract::find($contractId);
                    if (!$contract) {
                        $results[] = [
                            'id' => $contractId,
                            'success' => false,
                            'message' => 'Contract not found'
                        ];
                        continue;
                    }

                    switch ($action) {
                        case 'delete':
                            $contract->clearMediaCollection('attachments');
                            $contract->delete();
                            $results[] = [
                                'id' => $contractId,
                                'success' => true,
                                'message' => 'Contract deleted successfully'
                            ];
                            break;

                        case 'update_status':
                            $contract->update(['status' => $request->status]);
                            $results[] = [
                                'id' => $contractId,
                                'success' => true,
                                'message' => 'Contract status updated successfully'
                            ];
                            break;
                    }
                }

                DB::commit();
                return $results;
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (Exception $e) {
            throw new Exception("Failed to perform bulk operations: " . $e->getMessage());
        }
    }
}
