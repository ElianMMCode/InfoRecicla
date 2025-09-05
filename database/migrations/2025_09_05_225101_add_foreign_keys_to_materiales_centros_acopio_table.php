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
        Schema::table('materiales_centros_acopio', function (Blueprint $table) {
            $table->foreign(['centro_acopio_id'], 'fk_mca_cac')->references(['id'])->on('centros_acopio')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['material_id'], 'fk_mca_material')->references(['id'])->on('materiales')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materiales_centros_acopio', function (Blueprint $table) {
            $table->dropForeign('fk_mca_cac');
            $table->dropForeign('fk_mca_material');
        });
    }
};
