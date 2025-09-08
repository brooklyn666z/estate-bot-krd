<?php

return [
    // Один основной админ
    'admin_id' => env('TELEGRAM_ADMIN_ID'),

    // Или список админов через запятую: TELEGRAM_ADMIN_IDS=111,222,333
    'admin_ids' => array_values(array_filter(array_map('trim', explode(',', env('TELEGRAM_ADMIN_IDS', ''))))),
    'admin_access_key' => env('TELEGRAM_ADMIN_ACCESS_KEY', ''), // ключ входа /admin
];
