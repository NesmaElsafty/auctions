<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'national_id' => $this->national_id,
            'phone' => $this->phone,
            'address' => $this->address,
            'summary' => $this->summary,
            'link' => $this->link,
            'type' => $this->type,
            'is_active' => $this->is_active,
            'avatar_url' => $this->whenLoaded('media', function () {
                $media = $this->getFirstMedia('avatar');
                return $media ? $media->getFullUrl() : null;
            }, function () {
                $media = $this->getFirstMedia('avatar');
                return $media ? $media->getFullUrl() : null;
            }),

            'bank_account' => new BankAccountResource($this->whenLoaded('bank_account')),
            'agencies' => AgencyResource::collection($this->whenLoaded('agencies')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}


