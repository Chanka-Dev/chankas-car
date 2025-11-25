<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empleados', function (Blueprint $table) {
            $table->id('id_empleado');
            $table->string('ci', 20)->unique();
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('telefono', 20)->nullable();
            $table->unsignedBigInteger('id_cargo');
            $table->timestamps();

            $table->foreign('id_cargo')->references('id_cargo')->on('cargos')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};