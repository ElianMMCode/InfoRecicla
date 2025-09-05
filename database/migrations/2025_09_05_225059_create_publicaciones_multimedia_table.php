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
        Schema::create('publicaciones_multimedia', function (Blueprint $table) {
            $table->char('id', 36)->default('uuid()')->primary();
            $table->char('publicacion_id', 36)->index('idx_pub_multi_pub');
            $table->enum('tipo', ['imagen', 'video', 'documento', 'enlace']);
            $table->string('url', 400);
            $table->string('titulo', 150)->nullable();
            $table->string('descripcion', 500)->nullable();
            $table->dateTime('creado')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publicaciones_multimedia');
    }
};
