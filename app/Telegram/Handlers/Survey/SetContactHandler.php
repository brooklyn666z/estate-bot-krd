<?php

namespace App\Telegram\Handlers\Survey;

use App\Telegram\Survey\SurveyStorage;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;

class SetContactHandler
{
    public function __invoke(Nutgram $bot, string $value): void
    {
        $map = [
            'tg' => 'Телеграм',
            'wa' => 'Ватсап',
        ];

        $storage = new SurveyStorage($bot->userId());

        $value = $map[$value] ?? $value;
        $storage->update(['contact' => $value, 'step' => 4]);

        // Клавиатура "Поделиться контактом"

        $bot->answerCallbackQuery(text: 'Супер ✅');
        $bot->editMessageText(
            "4) Укажите номер телефона:",
            parse_mode: ParseMode::HTML
        );
    }
}
