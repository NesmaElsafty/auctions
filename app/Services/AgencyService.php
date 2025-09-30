<?php

namespace App\Services;

use App\Models\Agency;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AgencyService
{
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        $user = auth()->user();
        return Agency::query()->where('user_id', $user->id)->latest()->paginate($perPage);
    }

    public function create(array $data, array $files = []): Agency
    {
        $user = auth()->user();
        $data['user_id'] = $user->id;
        // data unset documents
        unset($data['documents']);
        $agency = Agency::create($data);        
        return $agency;
    }

    public function find(int $id): Agency
    {
        $user = auth()->user();
        $agency = Agency::where('user_id', $user->id)->find($id);
        return $agency;
    }

    public function update(int $id, array $data): Agency
    {
        $user = auth()->user();
        $data['user_id'] = $user->id;
        $agency = $this->find($id);

        
        // data unset documents
        unset($data['documents']);
        $agency->update($data);
        return $agency;
    }

    public function delete(int $id): void
    {
        $agency = $this->find($id);
        $agency->delete();
    }
}


