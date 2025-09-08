<?php

namespace App\Telegram\Handlers\Survey;

use App\Models\Lead; // если модель иначе названа — замени здесь и в routes
use App\Telegram\Services\AdminNotifier;
use App\Telegram\Survey\SurveyStorage;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class SavePhoneHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $storage = new SurveyStorage($bot->userId());
        $data = $storage->get();

        // Обрабатываем только если реально ждём телефон
        if (($data['step'] ?? null) !== 4) {
            return; // игнорим чужие сообщения
        }

        // 1) телефон из contact
        $phone = $bot->message()?->contact?->phone_number;

        // 2) или текстом
        if (!$phone) {
            $text = trim((string)($bot->message()?->text ?? ''));
            // быстрая проверка — только цифры/+, скобки, дефисы и пробелы
            if ($text === '' || !preg_match('~[0-9]{6,}~', $text)) {
                $bot->sendMessage('Похоже, номер некорректный. Введите ещё раз, пожалуйста.');
                return;
            }
            $phone = $text;
        }

        // Нормализация: оставим только цифры, приписываем +
        $digits = preg_replace('/\D+/', '', $phone ?? '');
        if (strlen($digits) < 10) {
            $bot->sendMessage('Номер слишком короткий. Укажите корректный номер, пожалуйста.');
            return;
        }
        $phone = '+'.$digits;

// Сохраняем лид
        Lead::create([
            'telegram_id' => $bot->userId(),
            'phone'       => $phone,
            'answers'     => [
                'type'    => $data['type'] ?? null,
                'credit'  => $data['credit'] ?? null,
                'contact' => $data['contact'] ?? null,
            ],
        ]);

// Уведомление админам
        $tgId   = (string)$bot->userId();
        $user   = $bot->user();
        $name   = trim(($user->first_name ?? '').' '.($user->last_name ?? ''));
        $username = $user->username ?? null;

        $answers = [
            'Тип'     => $data['type'] ?? '—',
            'Расчёт'  => $data['credit'] ?? '—',
            'Канал'   => $data['contact'] ?? '—',
        ];
        $answersTxt = collect($answers)->map(fn($v,$k) => "<b>{$k}:</b> ".e($v))->implode("\n");
        $uLine  = $name !== '' ? "<b>Имя:</b> ".e($name)."\n" : '';
        $uLine .= $username ? "<b>Username:</b> @".e($username)."\n" : '';

        $notifyText = "🆕 <b>Новая заявка</b>\n\n{$answersTxt}\n<b>Телефон:</b> {$phone}\n\n{$uLine}<b>Telegram ID:</b> <code>{$tgId}</code>\n<i>Время:</i> ".now();
        AdminNotifier::notify($bot, $notifyText, 'HTML');

        $sent = $bot->sendMessage(
            "Спасибо! Мы свяжемся с вами в {$data['contact']} и пришлём подборку. ✅",
            reply_markup: InlineKeyboardMarkup::make()
                ->addRow(
                    InlineKeyboardButton::make(
                        text: '🔄 Возврат в меню',
                        callback_data: 'survey_restart:'.$bot->message()->message_id // передаём id сообщения
                    )
                )
        );

// Сброс состояния
        $storage->reset();
    }
}
