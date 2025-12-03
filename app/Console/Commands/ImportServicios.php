<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Servicio;

class ImportServicios extends Command
{
    protected $signature = 'import:servicios {--dry-run : Ver qué se importaría sin guardar}';
    protected $description = 'Importa todos los servicios únicos desde import.csv con sus precios';

    public function handle()
    {
        $csv = storage_path('app/import.csv');
        
        if (!file_exists($csv)) {
            $this->error('No se encontró el archivo import.csv en storage/app/');
            return 1;
        }

        $this->info('📂 Leyendo archivo CSV...');
        
        $contenido = file_get_contents($csv);
        $contenido = iconv('ISO-8859-1', 'UTF-8//IGNORE', $contenido);
        $lineas = explode(PHP_EOL, $contenido);

        $servicios = [];

        // Procesar desde línea 4 (datos reales)
        for ($i = 4; $i < count($lineas); $i++) {
            $linea = trim($lineas[$i]);
            if (empty($linea)) continue;
            
            $campos = str_getcsv($linea, ';');
            
            // Índice 6 = TIPO DE TRABAJO, 7 = IMPORTE CLIENTE, 8 = IMPORTE TECNICO
            if (isset($campos[6]) && !empty(trim($campos[6]))) {
                $nombreServicio = trim($campos[6]);
                $importeCliente = isset($campos[7]) ? $this->parseImporte($campos[7]) : 0;
                $importeTecnico = isset($campos[8]) ? $this->parseImporte($campos[8]) : 0;
                
                if (!isset($servicios[$nombreServicio])) {
                    $servicios[$nombreServicio] = [
                        'cliente' => $importeCliente,
                        'tecnico' => $importeTecnico,
                        'conteo' => 1
                    ];
                } else {
                    // Mantener el precio más alto encontrado
                    $servicios[$nombreServicio]['cliente'] = max($servicios[$nombreServicio]['cliente'], $importeCliente);
                    $servicios[$nombreServicio]['tecnico'] = max($servicios[$nombreServicio]['tecnico'], $importeTecnico);
                    $servicios[$nombreServicio]['conteo']++;
                }
            }
        }

        $this->info('📊 Total de servicios únicos encontrados: ' . count($servicios));
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->warn('🔍 MODO DRY-RUN - No se guardará nada');
            $this->newLine();
            
            $this->table(
                ['Servicio', 'Precio Cliente', 'Precio Técnico', 'Veces usado'],
                collect($servicios)->take(30)->map(function($datos, $nombre) {
                    return [
                        $nombre,
                        'Bs ' . number_format($datos['cliente'], 2),
                        'Bs ' . number_format($datos['tecnico'], 2),
                        $datos['conteo']
                    ];
                })->toArray()
            );
            
            $this->info('... y ' . (count($servicios) - 30) . ' servicios más');
            return 0;
        }

        $this->info('💾 Actualizando servicios en la base de datos...');
        $bar = $this->output->createProgressBar(count($servicios));
        $bar->start();

        $actualizados = 0;
        $creados = 0;

        foreach ($servicios as $nombre => $datos) {
            $servicio = Servicio::where('nombre', $nombre)->first();
            
            if ($servicio) {
                // Actualizar precios
                $servicio->update([
                    'costo' => $datos['cliente'],
                    'comision' => $datos['tecnico']
                ]);
                $actualizados++;
            } else {
                // Crear nuevo servicio
                Servicio::create([
                    'nombre' => $nombre,
                    'costo' => $datos['cliente'],
                    'comision' => $datos['tecnico']
                ]);
                $creados++;
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Servicios actualizados: $actualizados");
        $this->info("✅ Servicios creados: $creados");
        $this->info("📊 Total procesados: " . count($servicios));

        return 0;
    }

    private function parseImporte($valor)
    {
        $limpio = trim($valor);
        $limpio = str_replace(',', '.', $limpio);
        return floatval($limpio);
    }
}
