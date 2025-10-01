<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankAccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'bank_name' => $this->bank_name,
            'account_name' => $this->account_name,
            'bank_address' => $this->bank_address,
            'IBAN' => $this->IBAN,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
