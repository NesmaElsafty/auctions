<?php

namespace App\Services;

use App\Models\SelectableData;
use App\Helpers\ExportHelper;

class SelectableDataService
{
    public function list($data)
    {
        $selectableData = SelectableData::query()->with('subCategoryInput');

        if (isset($data['search'])) {
            $selectableData->where(function ($query) use ($data) {
                $query->where('value', 'like', '%' . $data['search'] . '%')
                    ->orWhere('label', 'like', '%' . $data['search'] . '%');
            });
        }

        return $selectableData->get();
    }

    public function create($data)
    {
        $selectableData = new SelectableData();
        $selectableData->sub_category_input_id = $data['input_id'];
        $selectableData->value = $data['value'];
        $selectableData->label = $data['label'];
        $selectableData->save();

        return $selectableData->load('subCategoryInput');
    }

    public function update($data, $id)
    {
        $selectableData = SelectableData::find($id);
        $selectableData->sub_category_input_id = $data['input_id'] ?? $selectableData->sub_category_input_id;
        $selectableData->value = $data['value'] ?? $selectableData->value;
        $selectableData->label = $data['label'] ?? $selectableData->label;
        $selectableData->save();

        return $selectableData->load('subCategoryInput');
    }

    public function destroy($id)
    {
        return SelectableData::where('id', $id)->delete();
    }

    // get input options by sub category input id
    public function getInputOptions($subCategoryInputId)
    {
        return SelectableData::where('sub_category_input_id', $subCategoryInputId)->get();
    }

}
