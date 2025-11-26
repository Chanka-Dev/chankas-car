<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Empleado;
use Illuminate\Support\Facades\Storage;

class MapTecnicos extends Command
{
    protected $signature = 'import:map-tecnicos {file=import.csv}';
    protected $description = 'Mapea técnicos del CSV con empleados existentes en la BD';

    private $mapping = [];

    public function handle()
    {
        $filename = $this->argument('file');
        $path = storage_path('app/' . $filename);

        if (!file_exists($path)) {
            $this->error("❌ No se encuentra el archivo: {$path}");
            return 1;
        }

        // Obtener empleados existentes
        $empleados = Empleado::all();
        
        $this->info("👷 Empleados registrados en la BD:");
        $empleadosTable = $empleados->map(fn($e) => [
            'ID' => $e->id_empleado,
            'Nombre' => $e->nombre,
        ])->toArray();
        $this->table(['ID', 'Nombre'], $empleadosTable);

        // Leer técnicos del CSV
        $file = fopen($path, 'r');
        fgetcsv($file, 0, ';'); // Skip header 1
        fgetcsv($file, 0, ';'); // Skip header 2
        fgetcsv($file, 0, ';'); // Skip header 3

        $tecnicos_csv = [];
        while (($row = fgetcsv($file, 0, ';')) !== false) {
            if (empty(array_filter($row))) continue;
            $tecnico = trim($row[4] ?? '');
            if ($tecnico && $tecnico != 'CAJA' && $tecnico != 'caja') {
                $tecnicos_csv[] = $tecnico;
            }
        }
        fclose($file);

        $tecnicos_unicos = array_values(array_unique($tecnicos_csv));
        sort($tecnicos_unicos);
        
        // Filtrar CAJA
        $tecnicos_unicos = array_filter($tecnicos_unicos, function($t) {
            $lower = mb_strtolower($t);
            return !in_array($lower, ['caja', 'roberto']);
        });
        $tecnicos_unicos = array_values($tecnicos_unicos);

        $this->info("\n📋 Técnicos encontrados en CSV:");
        foreach ($tecnicos_unicos as $i => $tecnico) {
            $this->line("  [{$i}] {$tecnico}");
        }

        // Sugerencias automáticas
        $this->info("\n🤖 Sugerencias de mapeo automático:");
        $this->mapping = $this->autoMap($tecnicos_unicos, $empleados);

        $mappingTable = [];
        foreach ($this->mapping as $csv_name => $empleado_id) {
            $status = '';
            $nombre_bd = '';
            
            if ($empleado_id === 'SKIP') {
                $status = 'SKIP';
                $nombre_bd = '(no importar)';
            } elseif ($empleado_id === null) {
                $status = 'NUEVO';
                $nombre_bd = '(crear nuevo)';
            } else {
                $empleado = $empleados->firstWhere('id_empleado', $empleado_id);
                $status = $empleado_id;
                $nombre_bd = $empleado->nombre ?? '?';
            }
            
            $mappingTable[] = [
                'CSV' => $csv_name,
                'ID BD' => $status,
                'Nombre BD' => $nombre_bd,
            ];
        }
        $this->table(['Técnico CSV', 'ID BD', 'Nombre BD'], $mappingTable);

        // Preguntar si está OK
        if (!$this->confirm("\n¿Los mapeos se ven correctos?", true)) {
            $this->warn("Ajusta manualmente en el código o crea los empleados faltantes primero.");
            return 1;
        }

        // Guardar mapeo en archivo JSON
        $mapeoPath = storage_path('app/mapeo_tecnicos.json');
        file_put_contents($mapeoPath, json_encode($this->mapping, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        $this->info("✅ Mapeo guardado en: {$mapeoPath}");
        $this->info("🚀 Ahora puedes ejecutar: php artisan import:trabajos");

        return 0;
    }

    private function autoMap(array $tecnicos_csv, $empleados)
    {
        $mapping = [];

        foreach ($tecnicos_csv as $tecnico_csv) {
            // Normalizar caracteres especiales
            $tecnico_normalized = $this->normalizeString($tecnico_csv);
            
            // Mapeo manual para casos conocidos
            $manual_map = [
                'pablo veizaga chirari' => 3,
                'francisco gonzales contreras' => 4,
                'benjamin lopez chumacero' => 5,
                'benjamin lopez davalos' => 5,
                'pedro lopez chumacero' => 2,
                'antonio lopez davalos' => 1,
                'andres lopez chumacero' => 'SKIP',
                'horacion lopez chumacero' => 'SKIP',
            ];

            if (isset($manual_map[$tecnico_normalized])) {
                $mapping[$tecnico_csv] = $manual_map[$tecnico_normalized];
                continue;
            }

            // Buscar por similitud de nombre
            foreach ($empleados as $empleado) {
                if (stripos($tecnico_normalized, mb_strtolower($empleado->nombre)) !== false) {
                    $mapping[$tecnico_csv] = $empleado->id_empleado;
                    continue 2;
                }
            }

            // No se encontró coincidencia
            $mapping[$tecnico_csv] = 'SKIP';
        }

        return $mapping;
    }

    private function normalizeString($str)
    {
        $str = mb_strtolower($str);
        // Reemplazar caracteres con acento/diéresis
        $replacements = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'ñ' => 'n', 'ü' => 'u',
            'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u',
            'Ñ' => 'n', 'Ü' => 'u',
        ];
        return str_replace(array_keys($replacements), array_values($replacements), $str);
    }
}
