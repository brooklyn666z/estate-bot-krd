<?php

namespace App\Telegram\Handlers\Admin;

use App\Telegram\Broadcast\BroadcastDraft;
use SergiX44\Nutgram\Nutgram;

class BroadcastStartHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $uid = $bot->userId();
        if (!\isAdminId($uid)) {
            $bot->sendMessage("Доступ запрещён.");
            return;
        }

        (new BroadcastDraft($uid))->set([
            'step'   => 'wait_text',
            'text'   => null,
            'photos' => [],
        ]);

        $bot->answerCallbackQuery();
        $bot->sendMessage("Введите текст рассылки (поддерживается HTML):");
    }
}
