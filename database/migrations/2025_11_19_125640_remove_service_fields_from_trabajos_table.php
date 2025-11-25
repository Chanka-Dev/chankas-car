<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trabajos', function (Blueprint $table) {
            // Eliminar foreign key primero
            $table->dropForeign(['id_servicio']);
            
            // Eliminar columnas que ahora están en trabajo_servicios
            $table->dropColumn([
                'id_servicio',
                'importe_cliente',
                'importe_tecnico'
            ]);
            
            // Observaciones se queda en trabajos (son generales del trabajo)
        });
    }

    public function down(): void
    {
        Schema::table('trabajos', function (Blueprint $table) {
            $table->unsignedBigInteger('id_servicio')->after('fecha_recalificacion');
            $table->decimal('importe_cliente', 10, 2)->after('id_cliente');
            $table->decimal('importe_tecnico', 10, 2)->after('importe_cliente');
            
            $table->foreign('id_servicio')->references('id_servicio')->on('servicios')->onDelete('restrict');
        });
    }
};