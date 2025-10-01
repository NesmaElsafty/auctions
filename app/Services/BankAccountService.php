<?php

namespace App\Services;

use App\Models\BankAccount;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BankAccountService
{
    public function list()
    {
        $user = auth()->user();
        return $user->bank_account;
    }

    public function create(array $data)
    {
        $user = auth()->user();
        $data['user_id'] = $user->id;
        // update or create bank account if exist update else create
        $bankAccount = $user->bank_account;
        if ($bankAccount) {
            $bankAccount->update($data);
        } else {
            $bankAccount = $user->bank_account()->create($data);
        }
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
