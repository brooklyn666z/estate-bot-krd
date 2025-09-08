<?php

namespace App\Telegram\Handlers\Admin;

use App\Telegram\Broadcast\BroadcastDraft;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Input\InputMediaPhoto;
use SergiX44\Nutgram\Telegram\Types\Input\InputMedia;

use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;

class BroadcastPreviewHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $uid = $bot->userId();
        $draft = new BroadcastDraft($uid);
        $state = $draft->get();

        $text   = $state['text'] ?? '';
        $photos = $state['photos'] ?? [];

        // Превью админу
        if (!empty($photos)) {
            $media = [];
            foreach ($photos as $i => $fid) {
                $item = InputMediaPhoto::make(media:$fid);
                if ($i === 0 && $text) $item->caption = $text;
                if ($i === 0 && $text) $item->parse_mode = ParseMode::HTML;
                $media[] = $item;
            }
            $bot->sendMediaGroup($media);
        } else {
            $bot->sendMessage($text, parse_mode: ParseMode::HTML);
        }

        // Клавиатура подтверждения
        $kb = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('✅ Отправить', callback_data:'admin:broadcast:confirm'),
                InlineKeyboardButton::make('✏️ Изменить',  callback_data:'admin:broadcast:restart')
            );


        $bot->sendMessage("Это превью рассылки. Отправляем?", reply_markup:$kb);
    }
}
