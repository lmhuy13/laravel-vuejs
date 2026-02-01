<?php

use Illuminate\Support\Facades\Route;

Route::view('/welcome', 'welcome');

// Primary entry: show login first.
Route::redirect('/', '/login');

// Vue SPA entrypoint (history mode)
Route::view('/{any}', 'app')->where('any', '^(?!api|sanctum|admin-theme).*$');
