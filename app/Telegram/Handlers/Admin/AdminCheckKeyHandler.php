<?php

namespace App\Telegram\Handlers\Admin;

use App\Models\Admin;
use App\Telegram\Broadcast\BroadcastDraft;
use SergiX44\Nutgram\Nutgram;

class AdminCheckKeyHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $uid = $bot->userId();
        $draft = new BroadcastDraft($uid);
        $state = $draft->get();

        if (($state['step'] ?? '') !== 'wait_key') {
            return; // игнор, не ждём ключ
        }

        $got = trim((string)($bot->message()?->text ?? ''));
        $key = (string)config('telegram.admin_access_key');

        if ($key === '' || hash_equals($key, $got)) {
            Admin::firstOrCreate(['telegram_id' => $uid]);
            $draft->update(['step' => 'menu']);
            $bot->sendMessage("Успех ✅ Вы добавлены как админ.");
            (new AdminMenuHandler())($bot);
        } else {
            $bot->sendMessage("Ключ неверный. Повторите попытку или /cancel.");
        }
    }
}
