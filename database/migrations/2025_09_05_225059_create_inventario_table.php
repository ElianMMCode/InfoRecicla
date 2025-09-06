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
        Schema::create('inventario', function (Blueprint $table) {
            $table->char('id', 36)->default('uuid()')->primary();
            $table->char('punto_eca_id', 36);
            $table->char('material_id', 36)->index('fk_inv_material');
            $table->decimal('capacidad_max', 12, 3)->nullable();
            $table->decimal('stock_actual', 12, 3)->default(0);
            $table->decimal('umbral_alerta', 12, 3)->nullable();
            $table->decimal('umbral_critico', 12, 3)->nullable();
            $table->enum('unidad_medida', ['kg', 'unidad', 'l', 'm3'])->default('kg');
            $table->dateTime('creado')->useCurrent();
            $table->dateTime('actualizado')->useCurrentOnUpdate()->useCurrent();

            $table->unique(['punto_eca_id', 'material_id'], 'uq_inv_eca_material');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventario');
    }
};
