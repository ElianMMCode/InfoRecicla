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
        Schema::table('mensajes', function (Blueprint $table) {
            $table->foreign(['conversacion_id'], 'fk_mensajes_conversacion_id')->references(['id'])->on('conversaciones')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['remitente_id'], 'fk_mensajes_remitente_id')->references(['id'])->on('usuarios')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mensajes', function (Blueprint $table) {
            $table->dropForeign('fk_mensajes_conversacion_id');
            $table->dropForeign('fk_mensajes_remitente_id');
        });
    }
};
