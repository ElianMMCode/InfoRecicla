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
        Schema::create('publicaciones_etiquetas', function (Blueprint $table) {
            $table->char('publicacion_id', 36);
            $table->char('etiqueta_id', 36)->index('fk_pe_etiqueta');

            $table->primary(['publicacion_id', 'etiqueta_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publicaciones_etiquetas');
    }
};
