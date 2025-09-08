<?php

namespace App\Telegram\Handlers\Admin;

use App\Models\Admin;
use App\Telegram\Broadcast\BroadcastDraft;
use SergiX44\Nutgram\Nutgram;

class AdminEntryHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $uid = $bot->userId();
        if (\isAdminId($uid)) {
            (new BroadcastDraft($uid))->update(['step' => 'menu']);
            (new AdminMenuHandler())($bot);
            return;
        }

        (new BroadcastDraft($uid))->update(['step' => 'wait_key']);
        $bot->sendMessage("Введите ключ доступа:");
    }
}
