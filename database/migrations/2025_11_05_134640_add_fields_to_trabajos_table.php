<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trabajos', function (Blueprint $table) {
            $table->date('fecha_recalificacion')->nullable()->after('fecha_recepcion');
            $table->decimal('importe_cliente', 10, 2)->after('id_cliente');
            $table->decimal('importe_tecnico', 10, 2)->after('importe_cliente');
            $table->text('observaciones')->nullable()->after('importe_tecnico');
        });
    }

    public function down(): void
    {
        Schema::table('trabajos', function (Blueprint $table) {
            $table->dropColumn(['fecha_recalificacion', 'importe_cliente', 'importe_tecnico', 'observaciones']);
        });
    }
};