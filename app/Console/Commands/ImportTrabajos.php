<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Trabajo;
use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\TrabajoServicio;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ImportTrabajos extends Command
{
    protected $signature = 'import:trabajos {file=import.csv} {--dry-run : Solo simular sin importar}';
    protected $description = 'Importa trabajos desde el CSV a la base de datos';

    private $mapeo = [];
    private $stats = [
        'procesados' => 0,
        'importados' => 0,
        'omitidos' => 0,
        'clientes_nuevos' => 0,
        'servicios_nuevos' => 0,
        'errores' => 0,
    ];

    public function handle()
    {
        $filename = $this->argument('file');
        $path = storage_path('app/' . $filename);
        $mapeoPath = storage_path('app/mapeo_tecnicos.json');
        $isDryRun = $this->option('dry-run');

        if (!file_exists($path)) {
            $this->error("❌ No se encuentra el archivo: {$path}");
            return 1;
        }

        if (!file_exists($mapeoPath)) {
            $this->error("❌ No se encuentra el mapeo de técnicos. Ejecuta primero: php artisan import:map-tecnicos");
            return 1;
        }

        $this->mapeo = json_decode(file_get_contents($mapeoPath), true);
        
        // Normalizar claves del mapeo (trim)
        $normalized_mapeo = [];
        foreach ($this->mapeo as $key => $value) {
            $normalized_mapeo[trim($key)] = $value;
        }
        $this->mapeo = $normalized_mapeo;

        if ($isDryRun) {
            $this->warn("🧪 MODO SIMULACIÓN - No se guardará nada en la BD");
        }

        $this->info("🚀 Iniciando importación de trabajos...\n");

        // Leer CSV
        $file = fopen($path, 'r');
        fgetcsv($file, 0, ';'); // Skip header 1
        fgetcsv($file, 0, ';'); // Skip header 2
        fgetcsv($file, 0, ';'); // Skip header 3

        $progressBar = $this->output->createProgressBar();
        $progressBar->start();

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($file, 0, ';')) !== false) {
                if (empty(array_filter($row))) continue;

                $this->stats['procesados']++;
                $progressBar->advance();

                // Extraer datos del CSV
                $numero = trim($row[1] ?? '');
                $fecha = trim($row[2] ?? '');
                $fecha_reca = trim($row[3] ?? '');
                $tecnico_nombre = trim($row[4] ?? '');
                $placa = trim($row[5] ?? '');
                $tipo_trabajo = trim($row[6] ?? '');
                $importe = trim($row[7] ?? '0');
                $importe_tecnico = trim($row[8] ?? '0');
                $observaciones = trim($row[9] ?? '');
                $celular = trim($row[10] ?? '');

                // Validar datos mínimos
                if (empty($fecha) || empty($placa) || empty($tipo_trabajo)) {
                    $this->stats['omitidos']++;
                    continue;
                }

                // Verificar si el técnico está mapeado
                $tecnico_nombre = trim($tecnico_nombre); // Trim extra spaces
                
                if (!isset($this->mapeo[$tecnico_nombre])) {
                    $this->stats['omitidos']++;
                    continue;
                }

                $id_empleado = $this->mapeo[$tecnico_nombre];

                // Saltar si el técnico está marcado como SKIP
                if ($id_empleado === 'SKIP') {
                    $this->stats['omitidos']++;
                    continue;
                }

                if (!$isDryRun) {
                    // 1. Crear/obtener cliente
                    $cliente = $this->getOrCreateCliente($placa, $celular);

                    // 2. Crear/obtener servicio
                    $servicio = $this->getOrCreateServicio($tipo_trabajo, $importe);

                    // 3. Parsear fecha
                    $fecha_trabajo = $this->parseFecha($fecha);
                    $fecha_recepcion = $fecha_reca ? $this->parseFecha($fecha_reca) : $fecha_trabajo;

                    // 4. Crear trabajo
                    $trabajo = Trabajo::create([
                        'fecha_trabajo' => $fecha_trabajo,
                        'fecha_recepcion' => $fecha_recepcion,
                        'id_servicio' => $servicio->id_servicio,
                        'id_empleado' => $id_empleado,
                        'id_cliente' => $cliente->id_cliente,
                    ]);

                    // 5. Crear relación trabajo-servicio con importes
                    TrabajoServicio::create([
                        'id_trabajo' => $trabajo->id_trabajo,
                        'id_servicio' => $servicio->id_servicio,
                        'cantidad' => 1,
                        'importe_cliente' => $this->parseImporte($importe),
                        'importe_tecnico' => $this->parseImporte($importe_tecnico),
                        'observaciones' => $this->cleanText($observaciones),
                    ]);
                }

                $this->stats['importados']++;

                // Mostrar progreso cada 1000 registros
                if ($this->stats['procesados'] % 1000 == 0) {
                    $progressBar->clear();
                    $this->info("\n  📊 Procesados: {$this->stats['procesados']} | Importados: {$this->stats['importados']}");
                    $progressBar->display();
                }
            }

            fclose($file);
            $progressBar->finish();

            if (!$isDryRun) {
                DB::commit();
                $this->info("\n\n✅ Transacción confirmada - Datos guardados en la BD");
            } else {
                DB::rollBack();
                $this->info("\n\n🧪 Simulación completada - No se guardó nada");
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("\n\n❌ Error durante la importación: " . $e->getMessage());
            $this->error("Línea: " . $e->getLine());
            $this->error("Archivo: " . $e->getFile());
            return 1;
        }

        // Mostrar resumen
        $this->showSummary();

        return 0;
    }

    private function getOrCreateCliente($placa, $telefono)
    {
        $cliente = Cliente::where('placas', 'like', '%' . $placa . '%')->first();

        if (!$cliente) {
            $cliente = Cliente::create([
                'placas' => $placa,
                'telefono' => $telefono ?: null,
            ]);
            $this->stats['clientes_nuevos']++;
        }

        return $cliente;
    }

    private function getOrCreateServicio($nombre, $importe)
    {
        $nombre = $this->cleanText($nombre);
        
        if (empty($nombre)) {
            $nombre = 'SERVICIO GENERICO';
        }
        
        $servicio = Servicio::where('nombre', $nombre)->first();

        if (!$servicio) {
            $costo = $this->parseImporte($importe);
            $servicio = Servicio::create([
                'nombre' => $nombre,
                'costo' => $costo,
                'comision' => 0, // Se puede ajustar después
            ]);
            $this->stats['servicios_nuevos']++;
        }

        return $servicio;
    }

    private function cleanText($text)
    {
        if (empty($text)) {
            return null;
        }
        
        // Convertir a UTF-8 y limpiar caracteres problemáticos
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        $text = preg_replace('/[^\x20-\x7E]/', '', $text);
        $text = trim($text);
        
        return $text ?: null;
    }

    private function parseFecha($fecha)
    {
        // Formato: 30/12/2024
        try {
            return Carbon::createFromFormat('d/m/Y', $fecha)->format('Y-m-d');
        } catch (\Exception $e) {
            return now()->format('Y-m-d');
        }
    }

    private function parseImporte($importe)
    {
        // Limpiar y convertir a decimal
        $importe = str_replace([' ', ','], ['', '.'], $importe);
        return floatval($importe);
    }

    private function showSummary()
    {
        $this->info("\n📊 RESUMEN DE IMPORTACIÓN");
        $this->table(
            ['Concepto', 'Cantidad'],
            [
                ['Filas procesadas', number_format($this->stats['procesados'])],
                ['Trabajos importados', number_format($this->stats['importados'])],
                ['Trabajos omitidos', number_format($this->stats['omitidos'])],
                ['Clientes nuevos creados', number_format($this->stats['clientes_nuevos'])],
                ['Servicios nuevos creados', number_format($this->stats['servicios_nuevos'])],
                ['Errores', number_format($this->stats['errores'])],
            ]
        );

        $this->info("\n✨ Importación finalizada!");
    }
}
