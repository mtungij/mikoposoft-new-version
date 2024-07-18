<?php

use App\Http\Controllers\CustomViewsController;
use Illuminate\Support\Facades\Route;
use Spatie\Browsershot\Browsershot;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/loans/{loan}/view', [CustomViewsController::class,'loanDetails'])->name('loans.view');
