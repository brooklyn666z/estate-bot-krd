<?php

namespace App\Telegram\Handlers\Survey;

use App\Telegram\Survey\SurveyStorage;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class SetTypeHandler
{
    public function __invoke(Nutgram $bot, string $value): void
    {
        // value приходит из маршрута callback_data: survey:type:{value}
        $map = [
            't_studio' => 'Студия',
            't_1e2'    => 'Однокомнатная Евро 2',
            't_2'      => 'Двух комнатная',
            't_e3'     => 'Евро три',
        ];
        $storage = new SurveyStorage($bot->userId());
        $value = $map[$value] ?? $value; // $value приходит как короткий код
        $storage->update(['type' => $value, 'step' => 2]);


        // сразу готовим вопрос №2 (чтобы плавно перейти — это уже шаг 2, но оставлю тут вызов; сам вопрос реализуем ниже)
        $kb = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('Семейная, без ПВ',    callback_data:'survey:credit:fam_nodp'),
                InlineKeyboardButton::make('Семейная, маткапитал',callback_data:'survey:credit:fam_matcap')
            )
            ->addRow(
                InlineKeyboardButton::make('Семейная, ПВ до 1 млн', callback_data:'survey:credit:fam_pv_lt1m'),
                InlineKeyboardButton::make('Семейная, ПВ > 1 млн',  callback_data:'survey:credit:fam_pv_gt1m')
            );

        // редактируем предыдущее сообщение с новым вопросом
        $bot->answerCallbackQuery(text:'Принято ✅');
        $bot->editMessageText(
            '2) Какой у вас расчёт?',
            parse_mode: ParseMode::HTML
        );
        $bot->editMessageReplyMarkup(reply_markup:$kb);
    }
}
