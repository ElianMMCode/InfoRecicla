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
        Schema::create('compras', function (Blueprint $table) {
            $table->char('id', 36)->default('uuid()')->primary();
            $table->char('inventario_id', 36);
            $table->date('fecha');
            $table->decimal('kg', 12, 3);
            $table->decimal('precio_unit', 12)->nullable();
            $table->dateTime('creado')->useCurrent();

            $table->index(['inventario_id', 'fecha'], 'idx_compras_inv_fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
