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
        Schema::table('materiales', function (Blueprint $table) {
            $table->foreign(['categoria_id'], 'fk_mat_cat')->references(['id'])->on('categorias_material')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['tipo_id'], 'fk_mat_tipo')->references(['id'])->on('tipos_material')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materiales', function (Blueprint $table) {
            $table->dropForeign('fk_mat_cat');
            $table->dropForeign('fk_mat_tipo');
        });
    }
};
