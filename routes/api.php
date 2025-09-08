<?php
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;



Route::post('/telegram/webhook', [\App\Http\Controllers\WebhookController::class, 'webhook'])
    ->name('telegram.webhook');
