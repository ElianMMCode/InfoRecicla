<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mensajes', function (Blueprint $table) {
            $table->char('id', 36)->default('uuid()')->primary();
            $table->char('conversacion_id', 36)->index('fk_mensajes_conversacion_id');
            $table->char('remitente_id', 36)->index('fk_mensajes_remitente_id');
            $table->text('cuerpo');
            $table->dateTime('creado')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mensajes');
    }
};
