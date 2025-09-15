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
        Schema::create('ventas', function (Blueprint $table) {
            $table->char('id', 36)->default('uuid()')->primary();
            $table->char('inventario_id', 36);
            $table->decimal('cantidad', 12, 3);
            $table->date('fecha');
            $table->decimal('precio_venta', 12)->nullable();
            $table->char('centro_acopio_id', 36)->nullable()->index('fk_vent_cac');
            $table->index(['inventario_id', 'fecha'], 'idx_ventas_inv_fecha');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
