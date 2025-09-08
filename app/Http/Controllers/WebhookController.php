<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SergiX44\Nutgram\Nutgram;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class WebhookController extends Controller
{
    /**
     * Handle Telegram webhook update
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function webhook(Nutgram $bot, Request $request): void
    {
        // можно залогировать входящее обновление при отладке
        // \Log::debug('Telegram update', $request->all());

        $bot->run();
    }
}
