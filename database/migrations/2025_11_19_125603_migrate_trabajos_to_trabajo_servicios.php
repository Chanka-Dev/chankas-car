<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Migrar datos existentes de trabajos a trabajo_servicios
        $trabajos = DB::table('trabajos')->get();
        
        foreach ($trabajos as $trabajo) {
            DB::table('trabajo_servicios')->insert([
                'id_trabajo' => $trabajo->id_trabajo,
                'id_servicio' => $trabajo->id_servicio,
                'cantidad' => 1,
                'importe_cliente' => $trabajo->importe_cliente,
                'importe_tecnico' => $trabajo->importe_tecnico,
                'observaciones' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Vaciar la tabla trabajo_servicios
        DB::table('trabajo_servicios')->truncate();
    }
};