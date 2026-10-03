<?php

use App\Level3\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['app' => 'helpdesk', 'status' => 'ok']);
});

Route::get('/search/preview', [SearchController::class, 'preview']);
Route::get('/go', [SearchController::class, 'redirect']);
