<?php

namespace App\Helpers;

use App\Models\UserHistory;

class UserHistoryHelper
{
    public static function log($userId, $action, $description = null)
    {
        UserHistory::create([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
        ]);
    }
}