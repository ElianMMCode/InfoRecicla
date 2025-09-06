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
        Schema::table('puntos_eca', function (Blueprint $table) {
            $table->foreign(['gestor_id'], 'fk_eca_owner_usuario')->references(['id'])->on('usuarios')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('puntos_eca', function (Blueprint $table) {
            $table->dropForeign('fk_eca_owner_usuario');
        });
    }
};
