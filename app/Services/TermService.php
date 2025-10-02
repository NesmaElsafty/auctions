<?php

namespace App\Services;

use App\Models\Term;
use App\Helpers\ExportHelper;

class TermService
{
    public function UserIndex(array $data)
    {
        return Term::where(['is_active'=> true, 'segment'=> $data['segment'], 'type'=> $data['type']]);
    }

    public function adminIndex($data)
    {
        $term = Term::query();
        $term->where('type', $data['type']);
        if(isset($data['user_type'])) {
            $term->where('segment', $data['user_type']);
        }

        if(isset($data['search'])) {
            $term->where('title', 'like', '%' . $data['search'] . '%');
            $term->orWhere('content', 'like', '%' . $data['search'] . '%');
        }

        if(isset($data['sorted_by'])) {
            switch($data['sorted_by']) {
                case 'title':
                    $term->orderBy('title', 'asc');
                    break;
                case 'newest':
                    $term->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $term->orderBy('created_at', 'asc');
                    break;
            }
        }

        if(isset($data['is_active'])) {
            $term->where('is_active', $data['is_active']);
        }

        return $term;
    }

    public function create($data)
    {
        return Term::create($data);
    }

    public function update($data, $id)
    {
        $term = Term::find($id);
        $term->title = $data['title'] ?? $term->title;
        $term->content = $data['content'] ?? $term->content;
        $term->type = $data['type'] ?? $term->type;
        $term->segment = $data['segment'] ?? $term->segment;
        $term->is_active = $data['is_active'] ?? $term->is_active;
        $term->save();

        return $term;
    }

    public function destroy($id)
    {
        return Term::where('id', $id)->delete();
    }

    public function activationToggle($ids)
    {
        foreach($ids as $id) {
            $term = Term::find($id);
            $term->is_active = !$term->is_active;
            $term->save();
        }
    }

    public function export($ids)
    {
        $terms = Term::whereIn('id', $ids)->get();
        $csvData = [];
        foreach($terms as $term) {
            $csvData[] = [
                'title' => $term->title,
                'content' => $term->content,
                'is_active' => $term->is_active,
                'type' => $term->type,
                'segment' => $term->segment,
                'created_at' => $term->created_at,
            ];
        }

        $currentUser = auth()->user();
        $filename = 'terms_export_' . now()->format('Ymd_His') . '.csv';
        $media = ExportHelper::exportToMedia($csvData, $currentUser, 'exports', $filename);
        return $media->getFullUrl();
    }

    public function delete($ids)
    {
        return Term::whereIn('id', $ids)->delete();
    }
}
