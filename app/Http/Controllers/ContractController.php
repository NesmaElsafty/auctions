<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ContractService;
use Exception;
use App\Models\Contract;
class ContractController extends Controller
{
    protected $contractService;

    public function __construct(ContractService $contractService)
    {
        $this->contractService = $contractService;
    }

    /**
     * Display a listing of contracts
     */
    public function index(Request $request)
    {
        try {

            $contracts = $this->contractService->getAllContracts($request);
            return response()->json([
                "status" => "success",
                "message" => "Contract created successfully",
                "data" => $contracts
            ]);
        } catch (Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to list contracts",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified contract
     */
    public function show($id)
    {
        try {
            $contract = Contract::find($id);
            if(!$contract) {
                return response()->json([
                    "status" => "error",
                    "message" => "Contract not found"
                ], 404);
            }

            $content = file_get_contents($contract->file_path);

            return response()->json([
                "status" => "success",
                "message" => "Contract retrieved successfully",
                "data" => [
                    'contract' => $contract,
                    'content' => $content
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to show contract",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created contract
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'category_id' => 'nullable|exists:categories,id',
                'directory' => 'required|string',
                'file' => 'required|file|extensions:docx,txt|max:10240', // 10MB max
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);
            
            $contract = $this->contractService->createContract($request->all());
            
            $directory = $request->input('directory');
            // create directory if not exists
            if(!is_dir(public_path($directory))) {
                mkdir(public_path($directory), 0777, true);
            }
            $file = $request->file('file');
            $file->move(public_path($directory), $file->getClientOriginalName());

            return response()->json([
                "status" => "success",
                "message" => "Contract created successfully",
                "data" => $contract
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to create contract",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified contract
     */
    public function update(Request $request, $id)
    {
        try {
            $contract = $this->contractService->updateContract($request, $id);
            
            return response()->json([
                "status" => "success",
                "message" => "Contract updated successfully",
                "data" => $contract
            ]);
        } catch (Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to update contract",
                "error" => $e->getMessage()
            ], 500);    
        }
    }

    /**
     * Remove the specified contract
     */
    public function destroy($id)
    {
        try {
            $this->contractService->deleteContract($id);
            
            return response()->json([
                "status" => "success",
                "message" => "Contract deleted successfully"
            ]);
        } catch (Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to delete contract",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get contracts by category
     */
    public function getByCategory($categoryId)
    {
        try {
            $contracts = $this->contractService->getContractsByCategory($categoryId);
            
            return response()->json([
                "status" => "success",
                "message" => "Contracts retrieved successfully",
                "data" => $contracts
            ]);
        } catch (Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to retrieve contracts by category",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get contracts by status
     */
    public function getByStatus($status)
    {
        try {
            $contracts = $this->contractService->getContractsByStatus($status);
            
            return response()->json([
                "status" => "success",
                "message" => "Contracts retrieved successfully",
                "data" => $contracts
            ]);
        } catch (Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to retrieve contracts by status",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get expiring contracts
     */
    public function getExpiring(Request $request)
    {
        try {
            $days = $request->get('days', 30);
            $contracts = $this->contractService->getExpiringContracts($days);
            
            return response()->json([
                "status" => "success",
                "message" => "Expiring contracts retrieved successfully",
                "data" => $contracts
            ]);
        } catch (Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to retrieve expiring contracts",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update contract status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:draft,active,expired,terminated'
            ]);
            
            $contract = $this->contractService->updateContractStatus($id, $request->status);
            
            return response()->json([
                "status" => "success",
                "message" => "Contract status updated successfully",
                "data" => $contract
            ]);
        } catch (Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to update contract status",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get contract statistics
     */
    public function statistics()
    {
        try {
            $stats = $this->contractService->getContractStatistics();
            
            return response()->json([
                "status" => "success",
                "message" => "Contract statistics retrieved successfully",
                "data" => $stats
            ]);
        } catch (Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to retrieve contract statistics",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Perform bulk operations on contracts
     */
    public function bulkOperations(Request $request)
    {
        try {
            $results = $this->contractService->bulkOperations($request);
            
            return response()->json([
                "status" => "success",
                "message" => "Bulk operations completed",
                "data" => $results
            ]);
        } catch (Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Failed to perform bulk operations",
                "error" => $e->getMessage()
            ], 500);
        }
    }
}
