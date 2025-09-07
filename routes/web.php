<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ["Silence is gold."];
});

Route::get('/healths', fn() => response()->json(['ok'=>true,'ts'=>now()]));
