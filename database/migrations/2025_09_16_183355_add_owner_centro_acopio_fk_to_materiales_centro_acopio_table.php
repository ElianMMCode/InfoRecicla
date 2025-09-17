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
            //
            $table->foreign('centro_acopio_id')->references('id')->on('centros_acopio');
            $table->foreign('material_id')->references('id')->on('materiales');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materiales_centros_acopio', function (Blueprint $table) {
            //
            $table->dropForeign('fk_cac_materiales');
            $table->dropColumn('centro_acopio_id');
            $table->dropColumn('material_id');
        });
    }
};
