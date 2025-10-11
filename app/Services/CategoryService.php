<?php

namespace App\Services;

use App\Models\Category;
use App\Helpers\ExportHelper;

class CategoryService
{
    public function UserIndex(array $data)
    {
        return Category::where(['is_active'=> true])->get();
    }

    public function adminIndex($data)
    {
        $category = Category::query();

        if(isset($data['search'])) {
            $category->where('name', 'like', '%' . $data['search'] . '%');
        }

        if(isset($data['sorted_by'])) {
            switch($data['sorted_by']) {
                case 'name':
                    $category->orderBy('name', 'asc');
                    break;
                case 'newest':
                    $category->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $category->orderBy('created_at', 'asc');
                    break;
            }
        }

        if(isset($data['is_active'])) {
            $category->where('is_active', $data['is_active']);
        }

        return $category->get();
    }

    public function create($data)
    {
        $category = new Category();
        $category->name = $data['name'];
        $category->is_active = $data['is_active'] ?? true;
        $category->save();

        return $category;
    }

    public function update($data, $id)
    {
        $category = Category::find($id);
        $category->name = $data['name'] ?? $category->name;
        $category->is_active = $data['is_active'] ?? $category->is_active;
        $category->save();

        return $category;
    }

    public function destroy($id)
    {
        return Category::where('id', $id)->delete();
    }

    public function activationToggle($ids)
    {
        foreach($ids as $id) {
            $category = Category::find($id);
            $category->is_active = !$category->is_active;
            $category->save();
        }
    }

    public function export($ids)
    {
        $categories = Category::whereIn('id', $ids)->get();
        $csvData = [];
        foreach($categories as $category) {
            $csvData[] = [
                'name' => $category->name,
                'is_active' => $category->is_active,
                'created_at' => $category->created_at,
            ];
        }

        $currentUser = auth()->user();
        $filename = 'categories_export_' . now()->format('Ymd_His') . '.csv';
        $media = ExportHelper::exportToMedia($csvData, $currentUser, 'exports', $filename);
        return $media->getFullUrl();
    }

    public function delete($ids)
    {
        return Category::whereIn('id', $ids)->delete();
    }
}
