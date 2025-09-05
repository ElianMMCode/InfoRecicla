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
        Schema::table('publicaciones_multimedia', function (Blueprint $table) {
            $table->foreign(['publicacion_id'], 'fk_pub_multi_pub')->references(['id'])->on('publicaciones')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('publicaciones_multimedia', function (Blueprint $table) {
            $table->dropForeign('fk_pub_multi_pub');
        });
    }
};
