<?php

use App\Http\Controllers\KakologController;
use Illuminate\Support\Facades\Route;

Route::get('/kakolog/{jikkyo_id}', [KakologController::class, 'index']);
