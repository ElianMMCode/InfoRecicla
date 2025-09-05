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
        Schema::table('inventario', function (Blueprint $table) {
            $table->foreign(['punto_eca_id'], 'fk_inv_eca')->references(['id'])->on('puntos_eca')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['material_id'], 'fk_inv_material')->references(['id'])->on('materiales')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventario', function (Blueprint $table) {
            $table->dropForeign('fk_inv_eca');
            $table->dropForeign('fk_inv_material');
        });
    }
};
