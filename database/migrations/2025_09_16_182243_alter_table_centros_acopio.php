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
            // descripcion - correo - horario - materiales centro de acopio
            $table->string('descripcion');
            $table->dropColumn('nit');
            $table->string('correo');
            $table->string('horario');
            $table->string('materiales_centro_acopio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('centros_acopio', function (Blueprint $table) {
            //
        });
    }
};
