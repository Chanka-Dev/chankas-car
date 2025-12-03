<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Servicio;

class VerificarServicios extends Command
{
    protected $signature = 'servicios:verificar {--corregir : Corregir los precios duplicados}';
    protected $description = 'Verifica y corrige servicios con precios duplicados';

    public function handle()
    {
        $csv = storage_path('app/import.csv');
        
        $this->info('📂 Leyendo CSV para obtener precios correctos...');
        
        $contenido = file_get_contents($csv);
        $contenido = iconv('ISO-8859-1', 'UTF-8//IGNORE', $contenido);
        $lineas = explode(PHP_EOL, $contenido);

        $serviciosCSV = [];

        for ($i = 4; $i < count($lineas); $i++) {
            $linea = trim($lineas[$i]);
            if (empty($linea)) continue;
            
            $campos = str_getcsv($linea, ';');
            
            if (isset($campos[6]) && !empty(trim($campos[6]))) {
                $nombreServicio = trim($campos[6]);
                $importeCliente = $this->parseImporte($campos[7] ?? 0);
                $importeTecnico = $this->parseImporte($campos[8] ?? 0);
                
                if (!isset($serviciosCSV[$nombreServicio])) {
                    $serviciosCSV[$nombreServicio] = [
                        'cliente' => $importeCliente, 
                        'tecnico' => $importeTecnico
                    ];
                } else {
                    $serviciosCSV[$nombreServicio]['cliente'] = max(
                        $serviciosCSV[$nombreServicio]['cliente'], 
                        $importeCliente
                    );
                    $serviciosCSV[$nombreServicio]['tecnico'] = max(
                        $serviciosCSV[$nombreServicio]['tecnico'], 
                        $importeTecnico
                    );
                }
            }
        }

        $this->info('🔍 Comparando con base de datos...');
        $this->newLine();

        $serviciosBD = Servicio::all();
        $problematicos = [];

        foreach ($serviciosBD as $s) {
            if (isset($serviciosCSV[$s->nombre])) {
                $costoCSV = $serviciosCSV[$s->nombre]['cliente'];
                $comisionCSV = $serviciosCSV[$s->nombre]['tecnico'];
                
                // Verificar si está duplicado o es diferente
                if ($s->costo != $costoCSV || $s->comision != $comisionCSV) {
                    $esDuplicado = ($s->costo > 0 && abs($s->costo - ($costoCSV * 2)) < 0.01);
                    
                    $problematicos[] = [
                        'id' => $s->id_servicio,
                        'nombre' => $s->nombre,
                        'bd_costo' => $s->costo,
                        'bd_comision' => $s->comision,
                        'csv_costo' => $costoCSV,
                        'csv_comision' => $comisionCSV,
                        'duplicado' => $esDuplicado
                    ];
                }
            }
        }

        if (count($problematicos) == 0) {
            $this->info('✅ Todos los servicios tienen precios correctos');
            return 0;
        }

        $this->warn('⚠️  Servicios con diferencias: ' . count($problematicos));
        $this->newLine();

        $duplicados = collect($problematicos)->where('duplicado', true);
        $diferentes = collect($problematicos)->where('duplicado', false);

        if ($duplicados->count() > 0) {
            $this->error('🔴 DUPLICADOS (' . $duplicados->count() . ' servicios):');
            $this->table(
                ['Servicio', 'BD Costo', 'BD Comisión', 'CSV Costo', 'CSV Comisión'],
                $duplicados->take(10)->map(function($p) {
                    return [
                        substr($p['nombre'], 0, 30),
                        'Bs ' . number_format($p['bd_costo'], 2),
                        'Bs ' . number_format($p['bd_comision'], 2),
                        'Bs ' . number_format($p['csv_costo'], 2),
                        'Bs ' . number_format($p['csv_comision'], 2)
                    ];
                })->toArray()
            );
            if ($duplicados->count() > 10) {
                $this->line('... y ' . ($duplicados->count() - 10) . ' más');
            }
            $this->newLine();
        }

        if ($diferentes->count() > 0) {
            $this->warn('🟡 DIFERENTES (' . $diferentes->count() . ' servicios):');
            $this->table(
                ['Servicio', 'BD Costo', 'BD Comisión', 'CSV Costo', 'CSV Comisión'],
                $diferentes->take(10)->map(function($p) {
                    return [
                        substr($p['nombre'], 0, 30),
                        'Bs ' . number_format($p['bd_costo'], 2),
                        'Bs ' . number_format($p['bd_comision'], 2),
                        'Bs ' . number_format($p['csv_costo'], 2),
                        'Bs ' . number_format($p['csv_comision'], 2)
                    ];
                })->toArray()
            );
            if ($diferentes->count() > 10) {
                $this->line('... y ' . ($diferentes->count() - 10) . ' más');
            }
        }

        if ($this->option('corregir')) {
            $this->newLine();
            $this->info('🔧 Corrigiendo servicios...');
            
            $bar = $this->output->createProgressBar(count($problematicos));
            $bar->start();

            foreach ($problematicos as $p) {
                Servicio::where('id_servicio', $p['id'])->update([
                    'costo' => $p['csv_costo'],
                    'comision' => $p['csv_comision']
                ]);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);
            $this->info('✅ Servicios corregidos: ' . count($problematicos));
        } else {
            $this->newLine();
            $this->comment('💡 Ejecuta con --corregir para aplicar las correcciones');
        }

        return 0;
    }

    private function parseImporte($valor)
    {
        $limpio = trim($valor);
        $limpio = str_replace(',', '.', $limpio);
        return floatval($limpio);
    }
}
