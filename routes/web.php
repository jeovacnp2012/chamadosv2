<?php

use App\Exports\CalledsExport;
use App\Http\Controllers\SuperTabelaController;
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

// Rotas para SuperTabela
Route::get('/relatorios/supertabela', [SuperTabelaController::class, 'show'])
    ->name('relatorios.supertabela')
    ->middleware(['auth']);

Route::get('/chamados-abertos', [SuperTabelaController::class, 'chamadosAbertos'])
    ->name('relatorios.chamados-abertos')
    ->middleware(['web', 'auth']);

Route::get('/chamados-fechados', [SuperTabelaController::class, 'chamadosFechados'])
    ->name('relatorios.chamados-fechados')
    ->middleware(['web', 'auth']);
