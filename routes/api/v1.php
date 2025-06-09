<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Called;
use App\Models\Interaction;
Route::middleware('auth:sanctum')->get('/me', [\App\Http\Controllers\Api\AuthController::class, 'me']);
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    // 🔍 LISTAGEM já existe
    Route::get('/calleds', [\App\Http\Controllers\Api\CalledController::class, 'index']);

    // 👁️ VER UM REGISTRO
//    Route::get('/calleds/{called}', [\App\Http\Controllers\Api\CalledController::class, 'show']);
    // 👁️ VER UM REGISTRO ESPECIFICO COM OS SEUS RELACIONAMENTOS
    Route::get('/calleds/{called}', [\App\Http\Controllers\Api\CalledController::class, 'show']);

    // 📝 CRIAR
    Route::post('/calleds', [\App\Http\Controllers\Api\CalledController::class, 'store']);

    // ✏️ ATUALIZAR
    Route::put('/calleds/{called}', [\App\Http\Controllers\Api\CalledController::class, 'update']);

    // ❌ DELETAR
    Route::delete('/calleds/{called}', [\App\Http\Controllers\Api\CalledController::class, 'destroy']);

    //IMPLEMENTAR O UPLOAD DE ARQUIVOS
    Route::post('/calleds/{called}/interactions', [\App\Http\Controllers\Api\CalledController::class, 'storeInteraction']);
    Route::put('/profile/password', [\App\Http\Controllers\Api\AuthController::class, 'updatePassword']);
    Route::get('/calleds/{called}/interactions', [\App\Http\Controllers\Api\InteractionController::class, 'index']);
    Route::post('/calleds/{called}/interactions', [\App\Http\Controllers\Api\InteractionController::class, 'store']);
});

