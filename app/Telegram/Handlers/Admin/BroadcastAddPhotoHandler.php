<?php

namespace App\Telegram\Handlers\Admin;

use App\Telegram\Broadcast\BroadcastDraft;
use SergiX44\Nutgram\Nutgram;

class BroadcastAddPhotoHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $uid = $bot->userId();
        $draft = new BroadcastDraft($uid);
        $state = $draft->get();

        if (($state['step'] ?? '') !== 'collect_media') {
            return; // не в этапе сбора фото
        }

        $photo = $bot->message()?->photo; // массив размеров
        if (!$photo || \count($photo) === 0) {
            return;
        }
        // берём самое большое (последний элемент)
        $fileId = end($photo)->file_id ?? null;
        if (!$fileId) return;

        $photos = $state['photos'] ?? [];
        $photos[] = $fileId;
        $draft->update(['photos' => $photos]);

        $bot->sendMessage("Фото добавлено. Всего: ".count($photos).". Отправьте ещё или /done для предпросмотра.");
    }
}
