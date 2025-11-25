<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventario', function (Blueprint $table) {
            // Modificar nombre si es necesario (ya existe)
            // $table->string('nombre', 150)->change(); // Ya existe
            
            // Agregar nuevos campos
            $table->text('descripcion')->nullable()->after('nombre');
            $table->enum('unidad_medida', ['unidad', 'metro', 'kilo', 'litro', 'caja', 'par'])->default('unidad')->after('descripcion');
            $table->integer('stock_actual')->default(0)->after('cantidad');
            $table->integer('stock_minimo')->default(5)->after('stock_actual');
            $table->enum('tipo_stock', ['contable', 'pregunta'])->default('contable')->after('stock_minimo');
            $table->unsignedBigInteger('id_proveedor')->nullable()->after('tipo_stock');
            $table->date('fecha_ingreso')->nullable()->after('id_proveedor');
            
            // Renombrar 'cantidad' a algo más descriptivo si quieres
            // O dejarlo como está
            
            // Foreign key
            $table->foreign('id_proveedor')->references('id_proveedor')->on('proveedores')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('inventario', function (Blueprint $table) {
            $table->dropForeign(['id_proveedor']);
            $table->dropColumn([
                'descripcion',
                'unidad_medida',
                'stock_actual',
                'stock_minimo',
                'tipo_stock',
                'id_proveedor',
                'fecha_ingreso'
            ]);
        });
    }
};