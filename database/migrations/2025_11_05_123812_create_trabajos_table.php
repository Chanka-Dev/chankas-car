<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trabajos', function (Blueprint $table) {
            $table->id('id_trabajo');
            $table->date('fecha_trabajo');
            $table->date('fecha_recepcion');
            $table->unsignedBigInteger('id_servicio');
            $table->unsignedBigInteger('id_empleado');
            $table->unsignedBigInteger('id_cliente');
            $table->timestamps();

            $table->foreign('id_servicio')->references('id_servicio')->on('servicios')->onDelete('restrict');
            $table->foreign('id_empleado')->references('id_empleado')->on('empleados')->onDelete('restrict');
            $table->foreign('id_cliente')->references('id_cliente')->on('clientes')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trabajos');
    }
};