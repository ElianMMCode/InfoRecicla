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
        Schema::table('ventas', function (Blueprint $table) {
            $table->foreign(['centro_acopio_id'], 'fk_vent_cac')->references(['id'])->on('centros_acopio')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['inventario_id'], 'fk_venT_inv')->references(['id'])->on('inventario')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropForeign('fk_vent_cac');
            $table->dropForeign('fk_vent_inv');
        });
    }
};