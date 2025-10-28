<?php

use App\Http\Controllers\YourStyleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('filament.admin.auth.login');
});

Route::get('/yourstyle', [YourStyleController::class, 'index'])->name('yourstyle');
