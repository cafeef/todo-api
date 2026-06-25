<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Tarefas</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        h1 { text-align: center; color: #1a202c; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        
        /* Badges de Status com as cores oficiais do app.html */
        .badge { 
            padding: 4px 8px; 
            border-radius: 4px; 
            font-size: 11px; 
            font-weight: bold; 
            text-transform: uppercase; 
        }
        .concluida { background-color: #d1e7dd; color: #0a3622; }
        .em-andamento { background-color: #cfe2ff; color: #084298; }
        .pendente { background-color: #fff3cd; color: #856404; }
    </style>
</head>
<body>

    <h1>Lista de Tarefas</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Descrição</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tarefas as $tarefa)
                <tr>
                    <td>{{ $tarefa->id }}</td>
                    <td>{{ $tarefa->titulo ?? $tarefa->title }}</td>
                    <td>{{ $tarefa->descricao ?? $tarefa->description ?? 'Sem descrição' }}</td>
                    <td>
                        {{-- Validação dupla para garantir a correspondência exata do status --}}
                        @if($tarefa->status == 'concluida' || $tarefa->status == 'completed' || ($tarefa->completed ?? false))
                            <span class="badge concluida">Concluída</span>
                        @elseif($tarefa->status == 'em_andamento' || $tarefa->status == 'in_progress')
                            <span class="badge em-andamento">Em Andamento</span>
                        @else
                            <span class="badge pendente">Pendente</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>