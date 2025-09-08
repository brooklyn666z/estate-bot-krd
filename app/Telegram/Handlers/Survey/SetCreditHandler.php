<?php

namespace App\Telegram\Handlers\Survey;

use App\Telegram\Survey\SurveyStorage;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class SetCreditHandler
{
    public function __invoke(Nutgram $bot, string $value): void
    {
        $map = [
            'fam_nodp' => 'Ипотека семейная Без первоначального взноса',
            'fam_matcap' => 'Ипотека семейная Материнский капитал',
            'fam_pv_lt1m' => 'Ипотека семейная ПВ до 1 млн',
            'fam_pv_gt1m' => 'Ипотека семейная ПВ выше 1 млн',
        ];

        $storage = new SurveyStorage($bot->userId());

        $value = $map[$value] ?? $value;
        $storage->update(['credit' => $value, 'step' => 3]);

        // Готовим вопрос №3 (куда прислать)
        $kb = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('Телеграм', callback_data: 'survey:contact:tg'),
                InlineKeyboardButton::make('Ватсап', callback_data: 'survey:contact:wa')
            );

        $bot->answerCallbackQuery(text: 'Отлично ✅');
        $bot->editMessageText(
            '3) Куда вам прислать подборку объектов?',
            parse_mode: ParseMode::HTML
        );
        $bot->editMessageReplyMarkup(reply_markup: $kb);
    }
}
