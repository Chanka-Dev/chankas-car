<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trabajo_inventario', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_trabajo');
            $table->unsignedBigInteger('id_inventario');
            $table->decimal('cantidad_usada', 10, 2);
            $table->decimal('precio_unitario', 10, 2);
            $table->timestamps();

            $table->foreign('id_trabajo')->references('id_trabajo')->on('trabajos')->onDelete('cascade');
            $table->foreign('id_inventario')->references('id_inventario')->on('inventario')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trabajo_inventario');
    }
};