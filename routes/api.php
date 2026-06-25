<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TarefaController;
use Illuminate\Support\Facades\Route;

// Rotas públicas de autenticação
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Rotas protegidas por autenticação via Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // CRUD de Categorias
    Route::apiResource('categorias', CategoriaController::class);

    // CRUD de Tarefas (aceita ?status= e ?categoria_id= no index)
    Route::apiResource('tarefas', TarefaController::class);

    // Resumo estatístico das tarefas
    Route::get('/dashboard/resumo', [DashboardController::class, 'resumo']);
});
