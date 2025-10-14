<?php

namespace App\Services;

use App\Models\SubCategory;
use App\Helpers\ExportHelper;

class SubCategoryService
{
    public function list($data)
    {
        $subCategory = SubCategory::query();
        
        if(isset($data['search'])) {
            $subCategory->where('name', 'like', '%' . $data['search'] . '%');
        }

        if(isset($data['sorted_by'])) {
            switch($data['sorted_by']) {
                case 'name':
                    $subCategory->orderBy('name', 'asc');
                    break;
                case 'newest':
                    $subCategory->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $subCategory->orderBy('created_at', 'asc');
                    break;
            }
        }

        return $subCategory->get();
    }

    public function create($data)
    {
        $subCategory = new SubCategory();
        $subCategory->name = $data['name'];
        $subCategory->category_id = $data['category_id'];
        $subCategory->save();

        return $subCategory;
    }

    public function update($data, $id)
    {
        $subCategory = SubCategory::find($id);
        $subCategory->name = $data['name'] ?? $subCategory->name;
        $subCategory->category_id = $data['category_id'] ?? $subCategory->category_id;
        $subCategory->save();

        return $subCategory;
    }

    public function destroy($id)
    {
        $subCategory = SubCategory::find($id);
        $subCategory->delete();
        return true;
    }

    // get sub category by category id
    public function getSubCategoriesByCategoryId($categoryId)
    {
        return SubCategory::where('category_id', $categoryId)->get();
    }
}
