<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Servicio;

class CheckMantComisiones extends Command
{
    protected $signature = 'check:mant {--file= : Ruta relativa en storage/app (default import2.xlsx)}';
    protected $description = 'Compara comisiones de servicios MANT en Excel con la base de datos';

    public function handle()
    {
        $file = $this->option('file') ?: 'import2.xlsx';
        $path = storage_path('app/' . $file);
        if (!file_exists($path)) {
            $this->error('No se encontró el archivo: ' . $path);
            return 1;
        }

        $this->info('📂 Leyendo ' . $path);
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();
        $startRow = 4;

        $matches = [];
        for ($r = $startRow; $r <= $highestRow; $r++) {
            $nombre = trim((string)$sheet->getCell('D'.$r)->getCalculatedValue());
            if ($nombre === '') continue;
            if (stripos($nombre, 'MANT') !== false || stripos($nombre, 'MANTENIMIENTO') !== false) {
                $importeTecnico = $sheet->getCell('F'.$r)->getCalculatedValue();
                if (!is_numeric($importeTecnico)) {
                    $importeTecnico = floatval(preg_replace('/[^0-9.]/', '', (string)$importeTecnico));
                } else {
                    $importeTecnico = floatval($importeTecnico);
                }
                if (!isset($matches[$nombre])) {
                    $matches[$nombre] = ['csv_tecnico' => $importeTecnico, 'rows' => [$r]];
                } else {
                    $matches[$nombre]['csv_tecnico'] = max($matches[$nombre]['csv_tecnico'], $importeTecnico);
                    $matches[$nombre]['rows'][] = $r;
                }
            }
        }

        $this->info('🔍 Servicios MANT encontrados en Excel: ' . count($matches));
        $diffCount = 0;

        foreach ($matches as $nombre => $info) {
            $servBD = Servicio::where('nombre', $nombre)->first();
            $bdCom = $servBD ? $servBD->comision : null;
            $csvCom = $info['csv_tecnico'];
            $this->line("- $nombre");
            $this->line("  Excel (importe técnico): Bs " . number_format($csvCom,2) . " (filas: " . implode(',', $info['rows']) . ")");
            if ($servBD) {
                $this->line("  BD (comision): Bs " . number_format($bdCom,2) . " (id: $servBD->id_servicio)");
                if (abs($bdCom - $csvCom) > 0.01) {
                    $this->line("  -> DIFERENCIA");
                    $diffCount++;
                }
            } else {
                $this->line("  BD: NO EXISTE EN tabla servicios");
                $diffCount++;
            }
            $this->line('');
        }

        $this->info("Resumen: $diffCount diferencias (incluye servicios no existentes en BD).");
        return 0;
    }
}
