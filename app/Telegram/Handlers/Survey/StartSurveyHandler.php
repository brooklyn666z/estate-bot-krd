<?php

namespace App\Telegram\Handlers\Survey;

use App\Telegram\Survey\SurveyStorage;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class StartSurveyHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $storage = new SurveyStorage($bot->userId());
        $storage->reset();
        $storage->set([
            'step' => 1,
            'type' => null,
            'credit' => null,
            'contact' => null,
        ]);

        $kb = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make('Студия', callback_data: 'survey:type:t_studio'),
                InlineKeyboardButton::make('Однокомнатная (Евро 2)', callback_data: 'survey:type:t_1e2')
            )
            ->addRow(
                InlineKeyboardButton::make('Двухкомнатная', callback_data: 'survey:type:t_2'),
                InlineKeyboardButton::make('Евро три', callback_data: 'survey:type:t_e3')
            );

        $bot->sendMessage(
            '1) Выберите тип объекта:',
            parse_mode: ParseMode::HTML,
            reply_markup: $kb
        );
    }
}
