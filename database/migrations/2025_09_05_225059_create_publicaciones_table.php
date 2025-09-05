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
        Schema::create('publicaciones', function (Blueprint $table) {
            $table->char('id', 36)->default('uuid()')->primary();
            $table->char('usuario_id', 36);
            $table->string('titulo', 200);
            $table->text('contenido');
            $table->enum('estado', ['borrador', 'publicado', 'archivado'])->default('publicado');
            $table->char('categoria_id', 36)->nullable()->index('fk_pub_categoria');
            $table->dateTime('creado')->useCurrent();
            $table->dateTime('actualizado')->useCurrentOnUpdate()->useCurrent();

            $table->fullText(['titulo', 'contenido'], 'ftx_pub_titulo_contenido');
            $table->index(['usuario_id', 'estado'], 'idx_pub_usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publicaciones');
    }
};
