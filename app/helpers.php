<?php

use App\Models\Admin;

if (!function_exists('isAdminId')) {
    function isAdminId(int|string $telegramId): bool
    {
        $cfg = array_filter([
            config('telegram.admin_id'),
            ...config('telegram.admin_ids', []),
        ]);

        if (in_array((string)$telegramId, array_map('strval', $cfg), true)) {
            return true;
        }

        return Admin::where('telegram_id', $telegramId)->exists();
    }
}
