<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

//Route::get('/', function () {
//    return view('welcome');
//});
Route::get('/', function () {
    return redirect()->intended(Filament::getUrl());
});
