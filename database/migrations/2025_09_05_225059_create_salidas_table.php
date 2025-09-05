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
        Schema::create('salidas', function (Blueprint $table) {
            $table->char('id', 36)->default('uuid()')->primary();
            $table->char('inventario_id', 36);
            $table->date('fecha');
            $table->decimal('kg', 12, 3);
            $table->char('centro_acopio_id', 36)->nullable()->index('fk_sal_cac');
            $table->dateTime('creado')->useCurrent();

            $table->index(['inventario_id', 'fecha'], 'idx_salidas_inv_fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salidas');
    }
};
