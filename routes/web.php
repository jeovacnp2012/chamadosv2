<?php

use App\Exports\CalledsExport;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CalledController;
use App\Http\Controllers\Api\InteractionController;
use App\Http\Controllers\DataTableController;
use App\Http\Controllers\SuperTabelaControllerOld;
use App\Livewire\MigrationScreen;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/migracao', MigrationScreen::class)->name('migration.screen');

// Rotas web normais do seu sistema podem vir abaixo:
Route::get('/', function () {
    return view('welcome');
});

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
Route::get('/relatorios/supertabela', [SuperTabelaControllerOld::class, 'show'])
    ->name('relatorios.supertabela')
    ->middleware(['auth']);

Route::get('/chamados-abertos', [SuperTabelaControllerOld::class, 'chamadosAbertos'])
    ->name('relatorios.chamados-abertos')
    ->middleware(['web', 'auth']);

Route::get('/chamados-fechados', [SuperTabelaControllerOld::class, 'chamadosFechados'])
    ->name('relatorios.chamados-fechados')
    ->middleware(['web', 'auth']);

Route::get('/relatorios/datatables', [DataTableController::class, 'index'])
    ->name('relatorios.datatables')
    ->middleware(['web', 'auth']);



