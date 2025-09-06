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
        Schema::create('votos', function (Blueprint $table) {
            $table->char('id', 36)->default('uuid()')->primary();
            $table->enum('tipo', ['publicacion', 'punto_eca']);
            $table->char('referencia_id', 36);
            $table->char('usuario_id', 36)->index('fk_votos_usuario');
            $table->enum('valor', ['like', 'dislike']);
            $table->dateTime('creado')->useCurrent();

            $table->index(['tipo', 'referencia_id'], 'idx_voto_ref');
            $table->unique(['tipo', 'referencia_id', 'usuario_id'], 'uq_voto_unico');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votos');
    }
};
