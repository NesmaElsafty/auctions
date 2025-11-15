<?php

namespace App\Services;

use App\Models\BankAccount;
use App\Http\Resources\BankAccountResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BankAccountService
{
    public function list()
    {
        $user = auth()->user();
        return $user->bank_account;
    }

    public function updateOrCreate(int $userId, array $data)
    {
        $bankAccount = BankAccount::updateOrCreate(['user_id' => $userId], $data);
        return $bankAccount;
    }

    public function find(int $id)
    {
        $user = auth()->user();
        $bankAccount = $user->bank_account->find($id);
        if (!$bankAccount) {
            throw new ModelNotFoundException('Bank account not found');
        }
        return $bankAccount;
    }

    // public function update(int $id, array $data)
    // {
    //     $user = auth()->user();
    //     $bankAccount = $user->bank_account->find($id);
    //     $bankAccount->update($data);
    //     return $bankAccount;
    // }

    public function delete()
    {
        $user = auth()->user();
        $bankAccount = $user->bank_account->delete();
        return $bankAccount;
    }
}
