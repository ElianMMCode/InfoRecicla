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
        Schema::create('categorias_material', function (Blueprint $table) {
            $table->char('id', 36)->default('uuid()')->primary();
            $table->string('nombre', 80)->unique('uq_cat_mat_nombre');
            $table->string('descripcion', 300)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias_material');
    }
};
