<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::prefix('v1')->group(function () {
    require __DIR__ . '/api/v1.php'; // ✅ Caminho correto baseado na sua estrutura
});
// ROTA DE DEBUG - TESTAR LOGIN DIRETO
Route::post('/debug-login', function (Request $request) {
    // Instancia o controller e chama o método diretamente
    $controller = new AuthController();

    return $controller->login($request);
});

