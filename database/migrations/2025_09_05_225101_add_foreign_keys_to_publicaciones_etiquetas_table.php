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
        Schema::table('publicaciones_etiquetas', function (Blueprint $table) {
            $table->foreign(['etiqueta_id'], 'fk_pe_etiqueta')->references(['id'])->on('etiquetas')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['publicacion_id'], 'fk_pe_publicacion')->references(['id'])->on('publicaciones')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('publicaciones_etiquetas', function (Blueprint $table) {
            $table->dropForeign('fk_pe_etiqueta');
            $table->dropForeign('fk_pe_publicacion');
        });
    }
};
