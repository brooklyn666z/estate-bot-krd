<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ["Silence is gold."];
});


Route::post('/telegram/webhook', [\App\Http\Controllers\WebhookController::class, 'webhook'])
    ->name('telegram.webhook');
