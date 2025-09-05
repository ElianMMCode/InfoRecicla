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
        Schema::create('materiales_centros_acopio', function (Blueprint $table) {
            $table->char('centro_acopio_id', 36);
            $table->char('material_id', 36)->index('fk_mca_material');

            $table->primary(['centro_acopio_id', 'material_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materiales_centros_acopio');
    }
};
