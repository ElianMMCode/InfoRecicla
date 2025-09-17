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
        Schema::table('materiales_centros_acopio', function (Blueprint $table) {
            //
            $table->date('creado')->nullable();
            $table->date('actualizado')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materiales_centros_acopio', function (Blueprint $table) {
            //
        });
    }
};
