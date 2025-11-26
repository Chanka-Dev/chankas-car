<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Empleado;
use App\Models\Cliente;

class AnalyzeImportData extends Command
{
    protected $signature = 'import:analyze {file=import.csv}';
    protected $description = 'Analiza el archivo CSV de importación sin tocar la BD';

    public function handle()
    {
        $filename = $this->argument('file');
        $path = storage_path('app/' . $filename);

        if (!file_exists($path)) {
            $this->error("❌ No se encuentra el archivo: {$path}");
            $this->info("💡 Sube el CSV a: storage/app/{$filename}");
            return 1;
        }

        $this->info("📊 Analizando: {$filename}\n");

        // Leer CSV
        $file = fopen($path, 'r');
        
        // Saltar las primeras 3 filas de encabezado del Excel
        fgetcsv($file, 0, ';');
        fgetcsv($file, 0, ';');
        $headers = fgetcsv($file, 0, ';'); // Esta es la fila real de columnas
        
        $this->info("📋 Columnas encontradas:");
        $this->line("  [1] N°");
        $this->line("  [2] FECHA");
        $this->line("  [3] FECHA RECA");
        $this->line("  [4] TÉCNICO");
        $this->line("  [5] PLACA");
        $this->line("  [6] TIPO DE TRABAJO");
        $this->line("  [7] IMPORTE");
        $this->line("  [8] IMPORTE TÉCNICO");
        $this->line("  [12] PAGOS");

        // Estadísticas
        $stats = [
            'total_rows' => 0,
            'tecnicos' => [],
            'placas' => [],
            'fechas' => [],
            'tipos_servicio' => [],
            'tiene_pago' => 0,
            'trabajos_normales' => 0,
        ];

        $sample_rows = [];
        $row_count = 0;

        while (($row = fgetcsv($file, 0, ';')) !== false) {
            $row_count++;
            
            // Saltar filas vacías
            if (empty(array_filter($row))) {
                continue;
            }
            
            $stats['total_rows']++;

            // Guardar primeras 5 filas como muestra
            if (count($sample_rows) < 5) {
                $sample_rows[] = [
                    'N°' => $row[1] ?? '',
                    'Fecha' => $row[2] ?? '',
                    'Técnico' => $row[4] ?? '',
                    'Placa' => $row[5] ?? '',
                    'Tipo' => $row[6] ?? '',
                    'Importe' => $row[7] ?? '',
                    'Pago' => $row[12] ?? '',
                ];
            }

            // Analizar datos según la estructura real del CSV
            $fecha = $row[2] ?? '';
            $tecnico = $row[4] ?? '';
            $placa = $row[5] ?? '';
            $tipo_trabajo = $row[6] ?? '';
            $tiene_pago = !empty($row[12]) && $row[12] != '0';

            if ($fecha) $stats['fechas'][] = $fecha;
            if ($tecnico) $stats['tecnicos'][] = trim($tecnico);
            if ($placa) $stats['placas'][] = trim($placa);
            if ($tipo_trabajo) $stats['tipos_servicio'][] = $tipo_trabajo;

            // Clasificar si es pago o trabajo
            if ($tiene_pago) {
                $stats['tiene_pago']++;
            } else {
                $stats['trabajos_normales']++;
            }
        }
        fclose($file);

        // Mostrar muestra de datos
        $this->info("\n📌 Muestra de primeras 5 filas:");
        $this->table(
            ['N°', 'Fecha', 'Técnico', 'Placa', 'Tipo', 'Importe', 'Pago'],
            $sample_rows
        );

        // Mostrar estadísticas
        $this->info("\n📊 ESTADÍSTICAS:");
        $this->table(
            ['Concepto', 'Cantidad'],
            [
                ['Total de filas procesadas', $stats['total_rows']],
                ['Trabajos normales', $stats['trabajos_normales']],
                ['Trabajos con PAGO registrado', $stats['tiene_pago']],
                ['Técnicos únicos', count(array_unique(array_filter($stats['tecnicos'])))],
                ['Placas únicas', count(array_unique(array_filter($stats['placas'])))],
                ['Tipos de servicio únicos', count(array_unique(array_filter($stats['tipos_servicio'])))],
            ]
        );

        // Técnicos únicos
        $tecnicos_unicos = array_values(array_unique(array_filter($stats['tecnicos'])));
        sort($tecnicos_unicos);
        
        $this->info("\n👷 Técnicos encontrados en CSV:");
        foreach ($tecnicos_unicos as $tecnico) {
            if (empty($tecnico)) continue;
            
            $empleado = Empleado::where('nombre', 'like', '%' . trim($tecnico) . '%')->first();
            $status = $empleado ? "✅ Existe (ID: {$empleado->id_empleado})" : "❌ NO EXISTE EN BD";
            $color = $empleado ? 'info' : 'error';
            $this->line("  • {$tecnico} - {$status}");
        }

        // Placas únicas (primeras 20)
        $placas_unicas = array_values(array_unique(array_filter($stats['placas'])));
        sort($placas_unicas);
        
        $this->info("\n🚗 Primeras 20 placas encontradas:");
        foreach (array_slice($placas_unicas, 0, 20) as $placa) {
            if (empty($placa)) continue;
            
            $cliente = Cliente::where('placas', 'like', '%' . trim($placa) . '%')->first();
            $status = $cliente ? "✅ Existe" : "⚪ Nueva";
            $this->line("  • {$placa} - {$status}");
        }

        $this->info("\n✨ Análisis completado. Revisa los datos antes de importar.");
        
        return 0;
    }
}
