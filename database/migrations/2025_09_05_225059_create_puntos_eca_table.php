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
        Schema::create('puntos_eca', function (Blueprint $table) {
            $table->char('id', 36)->default('uuid()')->primary();
            $table->char('gestor_id', 36)->nullable()->index('fk_eca_owner_usuario');
            $table->string('nombre', 150);
            $table->string('descripcion', 500)->nullable();
            $table->string('direccion', 200)->nullable();
            $table->string('telefonoPunto', 20)->nullable();
            $table->string('correoPunto', 120)->nullable();
            $table->string('ciudad', 60)->nullable();
            $table->string('localidad', 60)->nullable();
            $table->decimal('latitud', 10, 6)->nullable();
            $table->decimal('longitud', 10, 6)->nullable();
            $table->string('nit', 20)->nullable();
            $table->string('horario_atencion', 150)->nullable();
            $table->string('sitio_web', 200)->nullable();
            $table->string('logo_url', 300)->nullable();
            $table->string('foto_url', 300)->nullable();
            $table->boolean('mostrar_mapa')->default(true);
            $table->enum('estado', ['activo', 'inactivo', 'bloqueado'])->default('activo');
            $table->dateTime('creado')->useCurrent();
            $table->dateTime('actualizado')->useCurrentOnUpdate()->useCurrent();

            $table->index(['ciudad', 'localidad'], 'idx_eca_localizacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puntos_eca');
    }
};
