<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\PagoTecnico;
use App\Models\GastoTaller;
use App\Models\Empleado;
use Carbon\Carbon;

class ImportPagosHistoricos extends Command
{
    protected $signature = 'import:pagos-historicos {file=import.csv} {--dry-run : Simular sin guardar}';
    protected $description = 'Importa pagos históricos desde el CSV (PAGO FRANCISCO, PAGO PEDRO, etc.)';

    // Mapeo de nombres en CSV a empleados en BD
    private $mapeo_empleados = [
        'BENJAMIN' => 'Benjamín',
        'SEBASTIAN' => 'Sebastián', 
        'PEDRO' => 'Pedro Antonio',
        'PABLO' => 'Pablo',
        'FRANCISCO' => 'Francisco',
        'HORACIO' => 'Horacio',
        'ANDRES' => 'Antonio', // Andres probablemente sea Antonio
    ];

    // Conceptos que son gastos del taller, no pagos a técnicos
    private $gastos_taller = [
        'RECALIFICADORA' => 'Pago a Recalificadora',
        'TRANSPORTE CILINDROS' => 'Transporte de Cilindros',
        'FOTOCOPIAS' => 'Fotocopias',
        'BUS' => 'Transporte - Bus',
        'JARDINERO' => 'Servicio de Jardinería',
    ];

    public function handle()
    {
        $filename = $this->argument('file');
        $path = storage_path('app/' . $filename);
        $dryRun = $this->option('dry-run');

        if (!file_exists($path)) {
            $this->error("❌ No se encuentra el archivo: {$path}");
            return 1;
        }

        $this->info("💰 Importando pagos históricos desde: {$filename}");
        if ($dryRun) {
            $this->warn("🔍 MODO DRY-RUN: No se guardará nada en la BD\n");
        }

        // Cargar empleados de la BD
        $empleados = Empleado::all()->keyBy('nombre');
        
        // Validar mapeo
        $this->info("👷 Validando mapeo de técnicos:");
        foreach ($this->mapeo_empleados as $csv => $nombre_bd) {
            if ($empleados->has($nombre_bd)) {
                $this->line("  ✅ {$csv} → {$nombre_bd} (ID: {$empleados[$nombre_bd]->id_empleado})");
            } else {
                $this->error("  ❌ {$csv} → {$nombre_bd} NO ENCONTRADO EN BD");
            }
        }

        // Leer CSV
        $file = fopen($path, 'r');
        
        // Saltar encabezados
        fgetcsv($file, 0, ';');
        fgetcsv($file, 0, ';');
        fgetcsv($file, 0, ';');

        $stats = [
            'total_pagos_tecnicos' => 0,
            'total_gastos_taller' => 0,
            'total_ignorados' => 0,
            'errores' => 0,
            'por_tecnico' => [],
            'por_concepto_gasto' => [],
        ];

        $this->info("\n📊 Procesando archivo...\n");
        $bar = $this->output->createProgressBar();

        while (($row = fgetcsv($file, 0, ';')) !== false) {
            $bar->advance();
            
            // Saltar filas vacías
            if (empty(array_filter($row))) {
                continue;
            }

            // Extraer datos
            $fecha_str = trim($row[2] ?? ''); // Índice 2: FECHA
            $tipo_trabajo = trim($row[6] ?? ''); // Índice 6: TIPO DE TRABAJO
            
            // Solo procesar filas que empiezan con "PAGO "
            if (!preg_match('/^PAGO\s+(.+)$/i', $tipo_trabajo, $matches)) {
                continue;
            }

            $nombre_pago = strtoupper(trim($matches[1]));
            
            // Ignorar "PAGO CON QR" (son anotaciones de trabajos normales)
            if ($nombre_pago === 'CON QR') {
                $stats['total_ignorados']++;
                continue;
            }

            // El importe está en índice 8 para pagos
            $importe_str = trim($row[8] ?? '');
            
            // Limpiar y parsear fecha
            try {
                $fecha = $this->parseFecha($fecha_str);
            } catch (\Exception $e) {
                $this->error("\n❌ Error en fecha: {$fecha_str}");
                $stats['errores']++;
                continue;
            }

            // Limpiar y parsear importe
            $importe = $this->parseImporte($importe_str);
            
            if ($importe <= 0) {
                $stats['total_ignorados']++;
                continue;
            }

            // Determinar si es pago a técnico o gasto del taller
            if (isset($this->mapeo_empleados[$nombre_pago])) {
                // Es un pago a técnico
                $nombre_empleado = $this->mapeo_empleados[$nombre_pago];
                
                if (!$empleados->has($nombre_empleado)) {
                    $this->error("\n❌ Empleado no encontrado: {$nombre_empleado}");
                    $stats['errores']++;
                    continue;
                }

                $empleado = $empleados[$nombre_empleado];
                
                if (!$dryRun) {
                    PagoTecnico::create([
                        'id_empleado' => $empleado->id_empleado,
                        'fecha_pago' => $fecha,
                        'monto_pagado' => $importe,
                        'periodo_inicio' => $fecha->copy()->startOfWeek(),
                        'periodo_fin' => $fecha->copy()->endOfWeek(),
                        'observaciones' => 'Pago semanal importado desde CSV histórico',
                        'tipo_pago' => 'completo',
                    ]);
                }

                $stats['total_pagos_tecnicos']++;
                $stats['por_tecnico'][$nombre_empleado] = ($stats['por_tecnico'][$nombre_empleado] ?? 0) + $importe;
                
            } elseif (isset($this->gastos_taller[$nombre_pago])) {
                // Es un gasto del taller
                $concepto = $this->gastos_taller[$nombre_pago];
                
                if (!$dryRun) {
                    GastoTaller::create([
                        'fecha' => $fecha,
                        'concepto' => $concepto,
                        'descripcion' => 'Importado desde CSV histórico',
                        'monto' => $importe,
                        'comprobante' => null,
                        'id_empleado' => null,
                    ]);
                }

                $stats['total_gastos_taller']++;
                $stats['por_concepto_gasto'][$concepto] = ($stats['por_concepto_gasto'][$concepto] ?? 0) + $importe;
                
            } else {
                // Tipo de pago desconocido
                $this->warn("\n⚠️  Pago desconocido: PAGO {$nombre_pago} (Bs {$importe})");
                $stats['total_ignorados']++;
            }
        }

        $bar->finish();
        fclose($file);

        // Mostrar resumen
        $this->info("\n\n✅ IMPORTACIÓN COMPLETADA\n");
        
        $this->table(
            ['Concepto', 'Cantidad'],
            [
                ['Pagos a Técnicos importados', $stats['total_pagos_tecnicos']],
                ['Gastos del Taller importados', $stats['total_gastos_taller']],
                ['Registros ignorados', $stats['total_ignorados']],
                ['Errores', $stats['errores']],
            ]
        );

        if (count($stats['por_tecnico']) > 0) {
            $this->info("\n💰 Total pagado por técnico:");
            $rows = [];
            foreach ($stats['por_tecnico'] as $tecnico => $total) {
                $rows[] = [$tecnico, 'Bs ' . number_format($total, 2)];
            }
            $this->table(['Técnico', 'Total'], $rows);
        }

        if (count($stats['por_concepto_gasto']) > 0) {
            $this->info("\n🧾 Total por concepto de gasto:");
            $rows = [];
            foreach ($stats['por_concepto_gasto'] as $concepto => $total) {
                $rows[] = [$concepto, 'Bs ' . number_format($total, 2)];
            }
            $this->table(['Concepto', 'Total'], $rows);
        }

        if ($dryRun) {
            $this->warn("\n⚠️  MODO DRY-RUN: No se guardó nada. Ejecuta sin --dry-run para importar.");
        } else {
            $this->info("\n✅ Datos guardados en la base de datos exitosamente.");
        }

        return 0;
    }

    private function parseFecha($fecha_str)
    {
        // Formato: DD/MM/YYYY
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', trim($fecha_str), $matches)) {
            return Carbon::createFromFormat('d/m/Y', trim($fecha_str));
        }
        
        throw new \Exception("Formato de fecha inválido: {$fecha_str}");
    }

    private function parseImporte($importe_str)
    {
        // Eliminar espacios y convertir coma a punto
        $clean = trim(str_replace([' ', ','], ['', '.'], $importe_str));
        
        return floatval($clean);
    }
}
