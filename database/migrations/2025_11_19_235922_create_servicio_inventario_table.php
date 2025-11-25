<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicio_inventario', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_servicio');
            $table->unsignedBigInteger('id_inventario');
            $table->integer('cantidad_base')->default(1);
            $table->boolean('es_opcional')->default(false);
            $table->timestamps();

            $table->foreign('id_servicio')->references('id_servicio')->on('servicios')->onDelete('cascade');
            $table->foreign('id_inventario')->references('id_inventario')->on('inventario')->onDelete('cascade');
            
            // Evitar duplicados
            $table->unique(['id_servicio', 'id_inventario']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicio_inventario');
    }
};