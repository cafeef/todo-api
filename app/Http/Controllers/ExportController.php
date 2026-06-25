<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    // Exportação para CSV (Nativa)
    public function exportCsv()
    {
        $tarefas = auth()->user()->tarefas()->with('categoria')->get();
        $fileName = 'tarefas.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Titulo', 'Status', 'Prazo', 'Categoria'];

        $callback = function() use($tarefas, $columns) {
            $file = fopen('php://output', 'w');
            
            // Adiciona a marca de bom para o Excel do Windows abrir com acentuação correta
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, $columns, ';'); // Usando ';' que é o padrão do Excel em português

            foreach ($tarefas as $tarefa) {
                fputcsv($file, [
                    $tarefa->id,
                    $tarefa->titulo,
                    $tarefa->status,
                    $tarefa->prazo,
                    $tarefa->categoria->nome ?? 'N/A'
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Exportação para PDF (DomPDF)
    public function exportPdf()
    {
        $tarefas = auth()->user()->tarefas()->with('categoria')->get();
        
        // Carrega a view Blade que vamos criar no Passo 3
        $pdf = Pdf::loadView('exports.tarefas_pdf', compact('tarefas'));
        
        return $pdf->download('minhas-tarefas.pdf');
    }
}