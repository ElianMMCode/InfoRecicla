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
        Schema::create('comentarios', function (Blueprint $table) {
            $table->char('id', 36)->default('uuid()')->primary();
            $table->enum('tipo', ['publicacion', 'punto_eca']);
            $table->char('referencia_id', 36);
            $table->char('usuario_id', 36)->index('fk_coment_usuario');
            $table->text('texto');
            $table->dateTime('creado')->useCurrent();

            $table->index(['tipo', 'referencia_id', 'creado'], 'idx_coment_ref');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comentarios');
    }
};
