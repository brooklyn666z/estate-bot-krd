<?php
/** @var SergiX44\Nutgram\Nutgram $bot */

use SergiX44\Nutgram\Nutgram;
use App\Telegram\Handlers\Survey\StartSurveyHandler;
use App\Telegram\Handlers\Survey\SetTypeHandler;
use App\Telegram\Handlers\Survey\SetCreditHandler;
use App\Telegram\Handlers\Survey\SetContactHandler;
use App\Telegram\Handlers\Survey\SavePhoneHandler;
use App\Telegram\Handlers\Admin\AdminEntryHandler;
use App\Telegram\Handlers\Admin\AdminCheckKeyHandler;
use App\Telegram\Handlers\Admin\AdminMenuHandler;
use App\Telegram\Handlers\Admin\BroadcastStartHandler;
use App\Telegram\Handlers\Admin\BroadcastSetTextHandler;
use App\Telegram\Handlers\Admin\BroadcastMediaChoiceHandler;
use App\Telegram\Handlers\Admin\BroadcastAddPhotoHandler;
use App\Telegram\Handlers\Admin\BroadcastDoneHandler;
use App\Telegram\Handlers\Admin\BroadcastPreviewHandler;
use App\Telegram\Handlers\Admin\BroadcastConfirmHandler;
// Команда для старта опроса
$bot->onCommand('start', StartSurveyHandler::class);



// Callback “вопрос 1 → выбор типа”
// Nutgram умеет разбирать шаблон {value} и передавать его в хэндлер
$bot->onCallbackQueryData(
    'survey:type:{value}',
    SetTypeHandler::class
);


$bot->onCallbackQueryData(
    'survey:credit:{value}',
    SetCreditHandler::class
);

$bot->onCallbackQueryData(
    'survey:contact:{value}',
    SetContactHandler::class
);

// Пришёл контакт
$bot->onContact(SavePhoneHandler::class);

// Текст с номером (любой текст, но хэндлер сам проверит step=4)
$bot->onMessage(SavePhoneHandler::class);
// вход в админку
$bot->onCommand('admin', AdminEntryHandler::class);

// ввод ключа
$bot->onMessage(AdminCheckKeyHandler::class);

// меню
$bot->onCallbackQueryData('admin:menu', AdminMenuHandler::class);

// запуск рассылки
$bot->onCallbackQueryData('admin:broadcast:start', BroadcastStartHandler::class);

// принятие текста
$bot->onMessage(BroadcastSetTextHandler::class);

$bot->onCallbackQueryData('admin:broadcast:nomedia', BroadcastMediaChoiceHandler::class);
$bot->onCallbackQueryData('admin:broadcast:addmedia', BroadcastMediaChoiceHandler::class);

// добавление фото (несколько)
$bot->onPhoto(BroadcastAddPhotoHandler::class);

// завершить ввод фото → превью
$bot->onCommand('done', BroadcastDoneHandler::class);


$bot->onCallbackQueryData('admin:broadcast:confirm', BroadcastConfirmHandler::class);
$bot->onCallbackQueryData('admin:broadcast:restart', BroadcastConfirmHandler::class);

$bot->onCallbackQueryData('survey_restart', StartSurveyHandler::class);
