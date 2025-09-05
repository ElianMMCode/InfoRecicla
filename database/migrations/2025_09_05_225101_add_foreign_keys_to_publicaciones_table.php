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
        Schema::table('publicaciones', function (Blueprint $table) {
            $table->foreign(['categoria_id'], 'fk_pub_categoria')->references(['id'])->on('categorias_publicaciones')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['usuario_id'], 'fk_pub_usuario')->references(['id'])->on('usuarios')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('publicaciones', function (Blueprint $table) {
            $table->dropForeign('fk_pub_categoria');
            $table->dropForeign('fk_pub_usuario');
        });
    }
};
