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
        Schema::create('guardados', function (Blueprint $table) {
            $table->char('id', 36)->default('uuid()')->primary();
            $table->char('usuario_id', 36);
            $table->enum('tipo', ['publicacion', 'punto_eca']);
            $table->char('referencia_id', 36);
            $table->dateTime('creado')->useCurrent();

            $table->index(['tipo', 'referencia_id'], 'idx_guardados_ref');
            $table->index(['usuario_id', 'tipo'], 'idx_guardados_usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardados');
    }
};
