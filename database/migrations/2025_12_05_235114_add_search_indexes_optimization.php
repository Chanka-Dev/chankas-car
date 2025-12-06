<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Agregar índices para optimizar búsquedas frecuentes
     */
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            // Índice en placas (búsqueda muy frecuente)
            $table->index('placas', 'idx_clientes_placas');
            // Índice en teléfono (búsqueda frecuente)
            $table->index('telefono', 'idx_clientes_telefono');
        });

        Schema::table('trabajos', function (Blueprint $table) {
            // Índice en fecha_trabajo (usado en reportes y filtros)
            $table->index('fecha_trabajo', 'idx_trabajos_fecha');
            // Índice compuesto para queries del mes
            $table->index(['fecha_trabajo', 'id_empleado'], 'idx_trabajos_fecha_empleado');
        });

        Schema::table('servicios', function (Blueprint $table) {
            // Índice en nombre (búsqueda por nombre)
            $table->index('nombre', 'idx_servicios_nombre');
        });

        Schema::table('empleados', function (Blueprint $table) {
            // Índice en CI (búsqueda por cédula)
            $table->index('ci', 'idx_empleados_ci');
            // Índice compuesto nombre+apellido (búsquedas frecuentes)
            $table->index(['nombre', 'apellido'], 'idx_empleados_nombre_completo');
        });

        Schema::table('inventario', function (Blueprint $table) {
            // Índice en nombre (búsqueda de piezas)
            $table->index('nombre', 'idx_inventario_nombre');
            // Índice para alertas de stock bajo
            $table->index(['stock_actual', 'stock_minimo'], 'idx_inventario_stock');
        });

        Schema::table('gastos_taller', function (Blueprint $table) {
            // Índice en fecha (reportes mensuales)
            $table->index('fecha', 'idx_gastos_fecha');
            // Índice en concepto (agrupaciones)
            $table->index('concepto', 'idx_gastos_concepto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropIndex('idx_clientes_placas');
            $table->dropIndex('idx_clientes_telefono');
        });

        Schema::table('trabajos', function (Blueprint $table) {
            $table->dropIndex('idx_trabajos_fecha');
            $table->dropIndex('idx_trabajos_fecha_empleado');
        });

        Schema::table('servicios', function (Blueprint $table) {
            $table->dropIndex('idx_servicios_nombre');
        });

        Schema::table('empleados', function (Blueprint $table) {
            $table->dropIndex('idx_empleados_ci');
            $table->dropIndex('idx_empleados_nombre_completo');
        });

        Schema::table('inventario', function (Blueprint $table) {
            $table->dropIndex('idx_inventario_nombre');
            $table->dropIndex('idx_inventario_stock');
        });

        Schema::table('gastos_taller', function (Blueprint $table) {
            $table->dropIndex('idx_gastos_fecha');
            $table->dropIndex('idx_gastos_concepto');
        });
    }
};
