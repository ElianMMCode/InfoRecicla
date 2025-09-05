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
        Schema::table('programacion_recoleccion', function (Blueprint $table) {
            $table->foreign(['centro_acopio_id'], 'fk_prog_rec_cac')->references(['id'])->on('centros_acopio')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['punto_eca_id'], 'fk_prog_rec_eca')->references(['id'])->on('puntos_eca')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['material_id'], 'fk_prog_rec_mat')->references(['id'])->on('materiales')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programacion_recoleccion', function (Blueprint $table) {
            $table->dropForeign('fk_prog_rec_cac');
            $table->dropForeign('fk_prog_rec_eca');
            $table->dropForeign('fk_prog_rec_mat');
        });
    }
};
