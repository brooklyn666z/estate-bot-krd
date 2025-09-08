<?php

namespace App\Telegram\Handlers\Admin;

use App\Telegram\Broadcast\BroadcastDraft;
use SergiX44\Nutgram\Nutgram;

class BroadcastDoneHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $uid = $bot->userId();
        if (!\isAdminId($uid)) return;

        $draft = new BroadcastDraft($uid);
        $state = $draft->get();

        if (($state['step'] ?? '') !== 'collect_media') return;

        $draft->update(['step' => 'confirm']);
        (new BroadcastPreviewHandler())($bot);
    }
}
