<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/healths', fn() => response()->json(['ok'=>true,'ts'=>now()]));
