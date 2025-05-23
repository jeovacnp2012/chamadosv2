<?php

use App\Exports\CalledsExport;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

//Route::get('/', function () {
//    return view('welcome');
//});
Route::get('/', function () {
    return redirect()->intended(Filament::getUrl());
});
Route::get('/exportar-chamados', function (\Illuminate\Http\Request $request) {
    return \Maatwebsite\Excel\Facades\Excel::download(
        new \App\Exports\CalledsExport($request),
        'chamados-filtrados.xlsx'
    );
})->name('exportar-chamados');
