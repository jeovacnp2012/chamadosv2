<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    require __DIR__ . '/api/v1.php'; // ✅ Caminho correto baseado na sua estrutura
});

