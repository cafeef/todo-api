<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Tarefa;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Cria 1 usuário de teste fixo para facilitar os testes manuais
        $user = User::factory()->create([
            'name'     => 'Usuário Teste',
            'email'    => 'teste@exemplo.com',
            'password' => 'senha123456',
        ]);

        // Cria 3 categorias para o usuário
        $categorias = [
            Categoria::create(['nome' => 'Trabalho', 'descricao' => 'Tarefas relacionadas ao trabalho',  'user_id' => $user->id]),
            Categoria::create(['nome' => 'Pessoal',  'descricao' => 'Tarefas pessoais do dia a dia',     'user_id' => $user->id]),
            Categoria::create(['nome' => 'Estudos',  'descricao' => 'Tarefas acadêmicas e aprendizado',  'user_id' => $user->id]),
        ];

        // Cria 6 tarefas distribuídas entre as categorias
        $tarefasData = [
            ['titulo' => 'Preparar relatório mensal',       'status' => 'em_andamento', 'prazo' => '2026-12-31', 'categoria_id' => $categorias[0]->id],
            ['titulo' => 'Reunião com o cliente',           'status' => 'pendente',     'prazo' => '2026-12-20', 'categoria_id' => $categorias[0]->id],
            ['titulo' => 'Consulta médica',                 'status' => 'pendente',     'prazo' => null,          'categoria_id' => $categorias[1]->id],
            ['titulo' => 'Pagar contas do mês',             'status' => 'concluida',    'prazo' => null,          'categoria_id' => $categorias[1]->id],
            ['titulo' => 'Estudar Laravel',                 'status' => 'em_andamento', 'prazo' => '2026-12-15', 'categoria_id' => $categorias[2]->id],
            ['titulo' => 'Entregar projeto final do curso', 'status' => 'pendente',     'prazo' => '2026-12-10', 'categoria_id' => $categorias[2]->id],
        ];

        foreach ($tarefasData as $dados) {
            Tarefa::create(array_merge($dados, ['user_id' => $user->id]));
        }
    }
}
