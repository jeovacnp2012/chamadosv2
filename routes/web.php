<?php

use App\Exports\CalledsExport;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CalledController;
use App\Http\Controllers\Api\InteractionController;
use App\Http\Controllers\DataTableController;
use App\Http\Controllers\SuperTabelaController;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

// Fallback para chamadas à rota 'login' (exigida por Sanctum)
Route::get('/login', function () {
    return response()->json(['message' => 'Login via navegador desabilitado. Use a API em /api/login.'], 403);
})->name('login');

// Rotas web normais do seu sistema podem vir abaixo:
Route::get('/', function () {
    return view('welcome');
});
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

Route::get('/relatorios/datatables', [DataTableController::class, 'index'])
    ->name('relatorios.datatables')
    ->middleware(['web', 'auth']);

