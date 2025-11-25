<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos_tecnicos', function (Blueprint $table) {
            $table->id('id_pago');
            $table->unsignedBigInteger('id_empleado');
            $table->date('fecha_pago');
            $table->decimal('monto_pagado', 10, 2);
            $table->date('periodo_inicio');
            $table->date('periodo_fin');
            $table->text('observaciones')->nullable();
            $table->enum('tipo_pago', ['completo', 'parcial', 'saldo'])->default('completo');
            $table->timestamps();

            $table->foreign('id_empleado')->references('id_empleado')->on('empleados')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos_tecnicos');
    }
};