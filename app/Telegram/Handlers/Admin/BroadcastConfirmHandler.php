<?php

namespace App\Telegram\Handlers\Admin;

use App\Models\Lead;
use App\Telegram\Broadcast\BroadcastDraft;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Input\InputMediaPhoto;

class BroadcastConfirmHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $data = $bot->callbackQuery()?->data;
        $uid = $bot->userId();
        if (!\isAdminId($uid)) {
            $bot->answerCallbackQuery(text: 'Нет доступа');
            return;
        }

        $draft = new BroadcastDraft($uid);
        $state = $draft->get();

        if ($data === 'admin:broadcast:restart') {
            $draft->set(['step' => 'wait_text', 'text' => null, 'photos' => []]);
            $bot->answerCallbackQuery();
            $bot->sendMessage("Ок, введите новый текст рассылки:");
            return;
        }



        if ($data === 'admin:broadcast:confirm') {

            $text = $state['text'] ?? '';
            $photos = $state['photos'] ?? [];

            $bot->answerCallbackQuery(text: 'Стартуем отправку');

            // Получатели: все уникальные telegram_id из leads
            $chatIds = Lead::query()
                ->select('telegram_id')
                ->whereNotNull('telegram_id')
                ->distinct()
                ->pluck('telegram_id')
                ->map(fn($v) => (int)$v)
                ->all();

            $sent = 0;
            $fail = 0;
            foreach ($chatIds as $chatId) {
                try {
                    if (!empty($photos)) {
                        $media = [];
                        foreach ($photos as $i => $fid) {
                            $item = InputMediaPhoto::make(media: $fid);
                            if ($i === 0 && $text) {
                                $item->caption = $text;
                                $item->parse_mode = ParseMode::HTML;
                            }
                            $media[] = $item;
                        }
                        $bot->sendMediaGroup($media, chat_id: $chatId);
                    } else {
                        $bot->sendMessage($text, chat_id: $chatId, parse_mode: ParseMode::HTML);
                    }
                    $sent++;
                    // Небольшая пауза, чтобы не словить FLOOD (при больших объёмах лучше очередь)
                    usleep(120000); // 0.12s
                } catch (\Throwable $e) {
                    $fail++;
                    Log::warning('Broadcast failed', ['chat_id' => $chatId, 'err' => $e->getMessage()]);
                    // продолжаем
                }
            }

            $bot->sendMessage("Рассылка завершена.\nУспешно: {$sent}\nОшибок: {$fail}");
            $draft->reset();
        }
    }
}
