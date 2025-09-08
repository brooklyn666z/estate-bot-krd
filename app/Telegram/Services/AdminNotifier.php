<?php

namespace App\Telegram\Services;

use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;

class AdminNotifier
{
    public static function notify(Nutgram $bot, string $text, ?string $parseMode = 'HTML'): void
    {
        $adminIds = config('telegram.admin_ids', []);
        $single   = config('telegram.admin_id');

        if ($single) {
            $adminIds[] = $single;
        }

        // uniq и фильтр пустых
        $adminIds = array_values(array_unique(array_filter($adminIds)));

        foreach ($adminIds as $chatId) {
            // безопасно ловим возможные ошибки (например, если админ не писал боту)
            try {
                $bot->sendMessage($text, chat_id: $chatId, parse_mode: $parseMode);
            } catch (\Throwable $e) {
                // по желанию: логнуть
                Log::warning('Admin notify failed', ['chat_id' => $chatId, 'err' => $e->getMessage()]);
            }
        }
    }
}
