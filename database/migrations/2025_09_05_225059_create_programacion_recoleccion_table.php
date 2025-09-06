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
        Schema::create('programacion_recoleccion', function (Blueprint $table) {
            $table->char('id', 36)->default('uuid()')->primary();
            $table->char('punto_eca_id', 36);
            $table->char('material_id', 36)->index('fk_prog_rec_mat');
            $table->char('centro_acopio_id', 36)->nullable()->index('fk_prog_rec_cac');
            $table->date('fecha');
            $table->time('hora')->nullable();
            $table->enum('frecuencia', ['manual', 'semanal', 'quincenal', 'mensual', 'unico'])->default('manual');
            $table->string('notas', 300)->nullable();
            $table->dateTime('creado')->useCurrent();

            $table->index(['punto_eca_id', 'fecha'], 'idx_prog_rec');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programacion_recoleccion');
    }
};
