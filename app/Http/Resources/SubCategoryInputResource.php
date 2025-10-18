<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\SelectableDataResource;

class SubCategoryInputResource extends JsonResource
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
            'name' => $this->name,
            'type' => $this->type,
            'placeholder' => $this->placeholder,
            'is_readonly' => $this->is_readonly,
            'is_required' => $this->is_required,
            'sub_category_id' => $this->sub_category_id,
            'category_id' => $this->subCategory->category_id,
            'input_options' => SelectableDataResource::collection($this->whenLoaded('selectableData')),
        ];
    }
}
