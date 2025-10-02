<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Helpers\ExportHelper;
class UserService
{
    public function users($data)
    {
        $users = User::query();
        
        // Use the type from data instead of hardcoding 'user'
        if(isset($data['type'])) {
            $users = $users->where('type', $data['type']);
        }

        if(isset($data['search'])) {
           $users->where('name', 'like', '%' . $data['search'] . '%')
           ->orWhere('national_id', 'like', '%' . $data['search'] . '%')
           ->orWhere('phone', 'like', '%' . $data['search'] . '%')
           ->orWhere('address', 'like', '%' . $data['search'] . '%');
        }

        if(isset($data['sorted_by'])) {
            switch($data['sorted_by']) {
                case 'name':
                    $users->orderBy('name', 'asc');
                    break;
                case 'oldest':
                    $users->orderBy('created_at', 'asc');
                    break;
                case 'newest':
                    $users->orderBy('created_at', 'desc');
                    break;
            }
        }

        if(isset($data['is_active'])) {
            $users->where('is_active', $data['is_active']);
        }
        return $users;
    }

    public function stats($data)
    {
        $type = $data['type'] ?? 'user';
        
        // Get current date and one month ago
        $today = now();
        $oneMonthAgo = now()->subMonth();
        
        // Base query for the specified type
        $baseQuery = User::where('type', $type);
        
        // 1. إجمالي المستخدمين الأفراد (Total Individual Users)
        $totalUsersToday = (clone $baseQuery)->count();
        $totalUsersOneMonthAgo = (clone $baseQuery)
            ->where('created_at', '<=', $oneMonthAgo)
            ->count();
        
        // 2. الحسابات النشطة (Active Accounts)
        $activeUsersToday = (clone $baseQuery)
            ->where('is_active', true)
            ->count();
        $activeUsersOneMonthAgo = (clone $baseQuery)
            ->where('is_active', true)
            ->where('created_at', '<=', $oneMonthAgo)
            ->count();
        
        // 3. الحسابات المحظورة (Blocked Accounts) - using soft deletes
        $blockedUsersToday = (clone $baseQuery)
            ->onlyTrashed()
            ->count();
        $blockedUsersOneMonthAgo = (clone $baseQuery)
            ->onlyTrashed()
            ->where('deleted_at', '<=', $oneMonthAgo)
            ->count();
        
        // Calculate percentage changes
        $totalChange = $this->calculatePercentageChange($totalUsersOneMonthAgo, $totalUsersToday);
        $activeChange = $this->calculatePercentageChange($activeUsersOneMonthAgo, $activeUsersToday);
        $blockedChange = $this->calculatePercentageChange($blockedUsersOneMonthAgo, $blockedUsersToday);
        
        return [
            'total_users' => [
                'current' => $totalUsersToday,
                'previous' => $totalUsersOneMonthAgo,
                'change_percentage' => $totalChange,
                'change_direction' => $totalChange >= 0 ? 'up' : 'down'
            ],
            'active_accounts' => [
                'current' => $activeUsersToday,
                'previous' => $activeUsersOneMonthAgo,
                'change_percentage' => $activeChange,
                'change_direction' => $activeChange >= 0 ? 'up' : 'down'
            ],
            'blocked_accounts' => [
                'current' => $blockedUsersToday,
                'previous' => $blockedUsersOneMonthAgo,
                'change_percentage' => $blockedChange,
                'change_direction' => $blockedChange >= 0 ? 'up' : 'down'
            ]
        ];
    }
    
    /**
     * Calculate percentage change between two values
     */
    private function calculatePercentageChange($oldValue, $newValue)
    {
        if ($oldValue == 0) {
            return $newValue > 0 ? 100 : 0;
        }
        
        return round((($newValue - $oldValue) / $oldValue) * 100, 2);
    }

    // block users (soft delete it)

    public function block($ids)
    {
        User::whereIn('id', $ids)->delete();
        return true;
    }

    public function unblock($ids)
    {
        User::whereIn('id', $ids)->restore();
        return true;
    }

    public function activationToggle($ids)
    {
        foreach($ids as $id) {
            $user = User::find($id);
            $user->is_active = !$user->is_active;
            $user->save();
        }
        return true;
    }

    public function export($ids)
    {
        $users = User::whereIn('id', $ids)->get();
        $csvData = [];
        foreach($users as $user) {
            if($user->type == 'user') {
                $csvData[] = [
                    'name' => $user->name,
                    'national_id' => $user->national_id,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    // 'auctions' => $user->auctions->count() ?? 0,
                    // 'purchases' => $user->purchases->count() ?? 0,
                    'is_active' => $user->is_active,
                    'created_at' => $user->created_at,
                ];
            }

            if($user->type == 'agent') {
                $csvData[] = [
                    'company_name' => $user->agencies?->pluck('name')->implode(','),
                    'name' => $user->name,
                    'national_id' => $user->national_id,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    // 'auctions' => $user->auctions->count() ?? 0,
                    // 'purchases' => $user->purchases->count() ?? 0,
                    'is_active' => $user->is_active,
                    'created_at' => $user->created_at,
                ];
            }
        }

       $currentUser = auth()->user();

        $filename = 'users_export_' . now()->format('Ymd_His') . '.csv';
        $media = ExportHelper::exportToMedia($csvData, $currentUser, 'exports', $filename);
        return $media->getFullUrl();
    }
}

