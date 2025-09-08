<?php

namespace App\Telegram\Handlers\Admin;

use App\Telegram\Broadcast\BroadcastDraft;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;

class BroadcastSetTextHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $uid = $bot->userId();
        $draft = new BroadcastDraft($uid);
        $state = $draft->get();

        if (($state['step'] ?? '') !== 'wait_text') {
            return;
        }

        $text = (string)($bot->message()?->text ?? '');
        if (trim($text) === '') {
            $bot->sendMessage("Текст пуст. Введите текст ещё раз:");
            return;
        }

        $draft->update(['text' => $text, 'step' => 'ask_media']);

        $kb = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('Без изображений', callback_data:'admin:broadcast:nomedia'),
                InlineKeyboardButton::make('Добавить изображения', callback_data:'admin:broadcast:addmedia')
            );

        $bot->sendMessage("Добавить изображения?", reply_markup:$kb);
    }
}
