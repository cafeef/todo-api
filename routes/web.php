<?php

use Illuminate\Support\Facades\Route;
use App\Models\Tarefa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

Route::get('/', function () {
    return view('welcome');
    
});

Route::get('/exportar-pdf', function(Request $request) {
    // 1. Recupera o token que o JavaScript enviou pela URL
    $tokenParam = $request->query('token');

    if (!$tokenParam) {
        abort(401, 'Acesso negado. Token não fornecido.');
    }

    // 2. Descriptografa e encontra o token no banco de dados do Sanctum
    $token = PersonalAccessToken::findToken($tokenParam);

    if (!$token || !$token->tokenable) {
        abort(401, 'Sessão expirada ou inválida. Faça login novamente.');
    }

    // 3. Captura o usuário dono deste token
    $usuario = $token->tokenable;

    // 4. CORREÇÃO AQUI: Filtra trazendo apenas as tarefas vinculadas a este usuário
    $tarefas = Tarefa::where('user_id', $usuario->id)->get(); 
    
    // 5. Gera o PDF passando os dados específicos dele
    $pdf = Pdf::loadView('exports.tarefas_pdf', compact('tarefas', 'usuario'));
    
    return $pdf->stream('meu_historico_de_tarefas.pdf');
});