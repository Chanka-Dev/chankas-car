<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trabajo_servicios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_trabajo');
            $table->unsignedBigInteger('id_servicio');
            $table->integer('cantidad')->default(1);
            $table->decimal('importe_cliente', 10, 2);
            $table->decimal('importe_tecnico', 10, 2);
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('id_trabajo')->references('id_trabajo')->on('trabajos')->onDelete('cascade');
            $table->foreign('id_servicio')->references('id_servicio')->on('servicios')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trabajo_servicios');
    }
};