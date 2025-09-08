<?php

namespace App\Telegram\Handlers\Admin;

use App\Telegram\Broadcast\BroadcastDraft;
use SergiX44\Nutgram\Nutgram;

class BroadcastMediaChoiceHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $data = $bot->callbackQuery()?->data;
        $uid = $bot->userId();
        if (!\isAdminId($uid)) { $bot->answerCallbackQuery(text:'Нет доступа'); return; }

        $draft = new BroadcastDraft($uid);
        $state = $draft->get();

        if (($state['step'] ?? '') !== 'ask_media') {
            $bot->answerCallbackQuery();
            return;
        }

        if ($data === 'admin:broadcast:nomedia') {
            $draft->update(['step' => 'confirm']);
            (new BroadcastPreviewHandler())($bot); // сразу превью
            return;
        }

        if ($data === 'admin:broadcast:addmedia') {
            $draft->update(['step' => 'collect_media', 'photos' => []]);
            $bot->answerCallbackQuery();
            $bot->sendMessage("Пришлите одно или несколько фото (по одному сообщению). Когда закончите — отправьте команду /done");
        }
    }
}
