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
        Schema::create('materiales', function (Blueprint $table) {
            $table->char('id', 36)->default('uuid()')->primary();
            $table->string('nombre', 120)->unique('uq_mat_nombre');
            $table->string('descripcion', 400)->nullable();
            $table->char('tipo_id', 36)->nullable()->index('fk_mat_tipo');
            $table->char('categoria_id', 36)->nullable()->index('fk_mat_cat');
            $table->string('imagen_url', 300)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materiales');
    }
};
