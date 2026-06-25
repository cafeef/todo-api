<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ApiResponse;

    public function resumo(Request $request): JsonResponse
    {
        $user = $request->user();
        $hoje = now()->startOfDay();

        $tarefas = $user->tarefas();

        $porStatus = [
            'pendente'     => (clone $tarefas)->where('status', 'pendente')->count(),
            'em_andamento' => (clone $tarefas)->where('status', 'em_andamento')->count(),
            'concluida'    => (clone $tarefas)->where('status', 'concluida')->count(),
        ];

        $atrasadas = (clone $tarefas)
            ->where('status', '!=', 'concluida')
            ->whereNotNull('prazo')
            ->whereDate('prazo', '<', $hoje)
            ->count();

        $porCategoria = $user->categorias()
            ->withCount('tarefas')
            ->orderBy('nome')
            ->get(['id', 'nome'])
            ->map(fn ($categoria) => [
                'id'             => $categoria->id,
                'nome'           => $categoria->nome,
                'total_tarefas'  => $categoria->tarefas_count,
            ]);

        return $this->successResponse('Resumo das tarefas gerado com sucesso.', [
            'total_tarefas'  => array_sum($porStatus),
            'por_status'     => $porStatus,
            'atrasadas'      => $atrasadas,
            'por_categoria'  => $porCategoria,
        ]);
    }
}
