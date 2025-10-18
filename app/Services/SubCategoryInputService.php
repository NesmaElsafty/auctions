<?php

namespace App\Services;

use App\Models\SubCategoryInput;
use App\Helpers\ExportHelper;

class SubCategoryInputService
{
    public function list($data)
    {
        $subCategoryInput = SubCategoryInput::query();
        
        if(isset($data['search'])) {
            $subCategoryInput->where('name', 'like', '%' . $data['search'] . '%');
        }

        if(isset($data['sorted_by'])) {
            switch($data['sorted_by']) {
                case 'name':
                    $subCategoryInput->orderBy('name', 'asc');
                    break;
                case 'newest':
                    $subCategoryInput->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $subCategoryInput->orderBy('created_at', 'asc');
                    break;
            }
        }

        return $subCategoryInput->get();
    }

    public function create($data)
    {
        $subCategoryInput = new SubCategoryInput();
        $subCategoryInput->sub_category_id = $data['sub_category_id'];
        $subCategoryInput->name = $data['name'];
        $subCategoryInput->type = $data['type'];
        $subCategoryInput->placeholder = $data['placeholder'];
        $subCategoryInput->is_readonly = $data['is_readonly'] ? 1 : 0;
        $subCategoryInput->is_required = $data['is_required'] ? 1 : 0;
        $subCategoryInput->save();

        return $subCategoryInput;
    }

    public function update($data, $id)
    {
        $subCategoryInput = SubCategoryInput::find($id);
        $subCategoryInput->name = $data['name'] ?? $subCategoryInput->name;
        $subCategoryInput->sub_category_id = $data['sub_category_id'] ?? $subCategoryInput->sub_category_id;
        $subCategoryInput->type = $data['type'] ?? $subCategoryInput->type;
        $subCategoryInput->placeholder = $data['placeholder'] ?? $subCategoryInput->placeholder;
        $subCategoryInput->is_readonly = $data['is_readonly'] ? 1 : 0 ?? $subCategoryInput->is_readonly;
        $subCategoryInput->is_required = $data['is_required'] ? 1 : 0;
        $subCategoryInput->save();

        return $subCategoryInput;
    }

    public function destroy($id)
    {
        $subCategoryInput = SubCategoryInput::find($id);
        $subCategoryInput->delete();
        return true;
    }

    // get sub category by category id
    public function getSubCategoryInputsBySubCategoryId($subCategoryId)
    {
        return SubCategoryInput::where('sub_category_id', $subCategoryId)->get();
    }
}
