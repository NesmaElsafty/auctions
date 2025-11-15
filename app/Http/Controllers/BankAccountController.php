<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\BankAccountResource;
use App\Services\BankAccountService;
use Exception;

class BankAccountController extends Controller
{
    public function __construct(private BankAccountService $service)
    {
    }

    public function index()
    {
        try {
            $bankAccount = $this->service->list(); 
            if (!$bankAccount) {
                return response()->json([
                    'message' => 'Bank account not found',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'message' => 'Bank accounts retrieved successfully',
                'data' => new BankAccountResource($bankAccount)
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to list bank accounts', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'bank_name' => ['nullable', 'string', 'max:255'],
                'account_name' => ['nullable', 'string', 'max:255'],
                'bank_address' => ['nullable', 'string', 'max:500'],
                'IBAN' => ['nullable', 'string', 'max:255'],
            ]);
            $user = auth()->user();

            $bankAccount = $this->service->updateOrCreate($user->id, $validated);
            return response()->json([
                'message' => 'Bank account updated successfully',
                'data' => new BankAccountResource($bankAccount)
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to update bank account', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $bankAccount = $this->service->find($id);
            return response()->json([
                'message' => 'Bank account retrieved successfully',
                'data' => new BankAccountResource($bankAccount)
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to fetch bank account', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy()
    {
        try {
            $bankAccount = auth()->user()->bank_account;
            if (!$bankAccount) {
                return response()->json([
                    'message' => 'Bank account not found',
                    'data' => null
                ], 404);
            }
            $this->service->delete();
            return response()->json(['message' => 'Bank account deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to delete bank account', 'error' => $e->getMessage()], 500);
        }
    }
}