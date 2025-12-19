<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TipoGasto;
use App\Models\GastoTaller;
use Illuminate\Support\Facades\DB;

class TiposGastosSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Obtener conceptos únicos de gastos existentes
        $conceptosExistentes = GastoTaller::select('concepto')
            ->distinct()
            ->orderBy('concepto')
            ->pluck('concepto');

        $this->command->info('Conceptos encontrados en gastos_taller: ' . $conceptosExistentes->count());

        // Crear tipos de gastos basados en los conceptos existentes
        foreach ($conceptosExistentes as $concepto) {
            if (!empty($concepto)) {
                TipoGasto::firstOrCreate(
                    ['nombre' => $concepto],
                    [
                        'descripcion' => 'Tipo de gasto migrado automáticamente desde registros existentes',
                        'activo' => true,
                    ]
                );
                $this->command->info('✓ Tipo de gasto creado: ' . $concepto);
            }
        }

        // Agregar algunos tipos de gastos comunes adicionales si no existen
        $tiposComunes = [
            ['nombre' => 'LUZ', 'descripcion' => 'Servicio de electricidad'],
            ['nombre' => 'AGUA', 'descripcion' => 'Servicio de agua potable'],
            ['nombre' => 'INTERNET', 'descripcion' => 'Servicio de internet'],
            ['nombre' => 'TELEFONO', 'descripcion' => 'Servicio telefónico'],
            ['nombre' => 'ALQUILER', 'descripcion' => 'Pago de alquiler del local'],
            ['nombre' => 'MATERIAL DE LIMPIEZA', 'descripcion' => 'Productos de limpieza'],
            ['nombre' => 'MATERIAL DE ESCRITORIO', 'descripcion' => 'Útiles de oficina'],
            ['nombre' => 'MANTENIMIENTO', 'descripcion' => 'Mantenimiento de equipos e instalaciones'],
            ['nombre' => 'COMBUSTIBLE', 'descripcion' => 'Gasolina y diesel'],
            ['nombre' => 'TRANSPORTE', 'descripcion' => 'Gastos de transporte'],
            ['nombre' => 'HERRAMIENTAS', 'descripcion' => 'Compra de herramientas'],
            ['nombre' => 'REPUESTOS', 'descripcion' => 'Compra de repuestos'],
        ];

        $this->command->info("\nAgregando tipos de gastos comunes...");
        
        foreach ($tiposComunes as $tipo) {
            $created = TipoGasto::firstOrCreate(
                ['nombre' => $tipo['nombre']],
                [
                    'descripcion' => $tipo['descripcion'],
                    'activo' => true,
                ]
            );

            if ($created->wasRecentlyCreated) {
                $this->command->info('✓ Tipo de gasto común agregado: ' . $tipo['nombre']);
            }
        }

        $this->command->info("\n✅ Proceso completado. Total de tipos de gastos: " . TipoGasto::count());
    }
}
