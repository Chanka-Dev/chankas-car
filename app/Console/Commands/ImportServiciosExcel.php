<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Servicio;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportServiciosExcel extends Command
{
    protected $signature = 'import:servicios-excel {--dry-run : Ver qué se importaría sin guardar}';
    protected $description = 'Importa todos los servicios desde import2.xlsx';

    public function handle()
    {
        $archivo = storage_path('app/import2.xlsx');
        
        if (!file_exists($archivo)) {
            $this->error('No se encontró el archivo import2.xlsx en storage/app/');
            return 1;
        }

        $this->info('📂 Leyendo archivo Excel...');
        
        try {
            $spreadsheet = IOFactory::load($archivo);
            $worksheet = $spreadsheet->getActiveSheet();
            
            $this->info('📊 Hoja activa: ' . $worksheet->getTitle());
            $this->newLine();
            
            // Primero veamos las primeras filas para identificar estructura
            $this->info('🔍 Analizando estructura...');
            $highestColumn = $worksheet->getHighestColumn();
            $this->table(
                ['Fila', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'],
                collect(range(1, 5))->map(function($row) use ($worksheet) {
                    $cols = [$row];
                    foreach(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'] as $col) {
                        $value = $worksheet->getCell($col . $row)->getValue();
                        $cols[] = substr($value ?? '', 0, 15);
                    }
                    return $cols;
                })->toArray()
            );
            
            $this->newLine();
            
            // Preguntar en qué fila empiezan los datos
            $filaInicio = $this->ask('¿En qué fila empiezan los datos (después del encabezado)?', 4);
            $columnaServicio = $this->ask('¿Columna del servicio (A, B, C...)?', 'G');
            $columnaImporteCliente = $this->ask('¿Columna del IMPORTE cliente?', 'H');
            $columnaImporteTecnico = $this->ask('¿Columna del IMPORTE TECNICO?', 'I');
            
            $this->info('💾 Procesando servicios...');
            
            $servicios = [];
            $filaActual = $filaInicio;
            $highestRow = $worksheet->getHighestRow();
            
            $bar = $this->output->createProgressBar($highestRow - $filaInicio + 1);
            $bar->start();
            
            while ($filaActual <= $highestRow) {
                $nombreServicio = trim($worksheet->getCell($columnaServicio . $filaActual)->getValue() ?? '');
                
                if (!empty($nombreServicio)) {
                    $importeCliente = $this->parseValor($worksheet->getCell($columnaImporteCliente . $filaActual)->getValue());
                    $importeTecnico = $this->parseValor($worksheet->getCell($columnaImporteTecnico . $filaActual)->getValue());
                    
                    if (!isset($servicios[$nombreServicio])) {
                        $servicios[$nombreServicio] = [
                            'cliente' => $importeCliente,
                            'tecnico' => $importeTecnico,
                            'conteo' => 1
                        ];
                    } else {
                        $servicios[$nombreServicio]['cliente'] = max($servicios[$nombreServicio]['cliente'], $importeCliente);
                        $servicios[$nombreServicio]['tecnico'] = max($servicios[$nombreServicio]['tecnico'], $importeTecnico);
                        $servicios[$nombreServicio]['conteo']++;
                    }
                }
                
                $filaActual++;
                $bar->advance();
            }
            
            $bar->finish();
            $this->newLine(2);
            
            $this->info('📊 Total de servicios únicos encontrados: ' . count($servicios));
            $this->newLine();

            if ($this->option('dry-run')) {
                $this->warn('🔍 MODO DRY-RUN - No se guardará nada');
                $this->newLine();
                
                $this->table(
                    ['Servicio', 'Precio Cliente', 'Precio Técnico', 'Veces usado'],
                    collect($servicios)->take(30)->map(function($datos, $nombre) {
                        return [
                            substr($nombre, 0, 40),
                            'Bs ' . number_format($datos['cliente'], 2),
                            'Bs ' . number_format($datos['tecnico'], 2),
                            $datos['conteo']
                        ];
                    })->toArray()
                );
                
                if (count($servicios) > 30) {
                    $this->info('... y ' . (count($servicios) - 30) . ' servicios más');
                }
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
                    $servicio->update([
                        'costo' => $datos['cliente'],
                        'comision' => $datos['tecnico']
                    ]);
                    $actualizados++;
                } else {
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
            
        } catch (\Exception $e) {
            $this->error('Error al leer el archivo Excel: ' . $e->getMessage());
            return 1;
        }
    }

    private function parseValor($valor)
    {
        if (is_numeric($valor)) {
            return floatval($valor);
        }
        
        $limpio = trim($valor ?? '');
        $limpio = str_replace(',', '.', $limpio);
        $limpio = preg_replace('/[^0-9.]/', '', $limpio);
        
        return floatval($limpio);
    }
}
