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
        Schema::table('centros_acopio', function (Blueprint $table) {
            $table->foreign(['owner_punto_eca_id'], 'fk_cac_owner_eca')->references(['id'])->on('puntos_eca')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('centros_acopio', function (Blueprint $table) {
            $table->dropForeign('fk_cac_owner_eca');
        });
    }
};
