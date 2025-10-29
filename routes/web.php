<?php

use App\Http\Controllers\YourStyleController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route('yourstyle');
});

Route::get('/yourstyle', [YourStyleController::class, 'index'])->name('yourstyle');
