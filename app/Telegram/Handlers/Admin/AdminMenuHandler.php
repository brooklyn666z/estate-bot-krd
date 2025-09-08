<?php

namespace App\Telegram\Handlers\Admin;

use App\Telegram\Broadcast\BroadcastDraft;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;

class AdminMenuHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $uid = $bot->userId();
        if (!\isAdminId($uid)) {
            $bot->sendMessage("Доступ запрещён.");
            return;
        }

        $kb = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('🚀 Новая рассылка', callback_data:'admin:broadcast:start')
            );

        $bot->sendMessage("Меню админа:", reply_markup:$kb);
    }
}
