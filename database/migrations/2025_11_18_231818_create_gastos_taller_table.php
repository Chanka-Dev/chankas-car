<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gastos_taller', function (Blueprint $table) {
            $table->id('id_gasto');
            $table->date('fecha');
            $table->string('concepto', 150);
            $table->text('descripcion')->nullable();
            $table->decimal('monto', 10, 2);
            $table->string('comprobante', 100)->nullable();
            $table->unsignedBigInteger('id_empleado')->nullable();
            $table->timestamps();

            $table->foreign('id_empleado')->references('id_empleado')->on('empleados')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gastos_taller');
    }
};