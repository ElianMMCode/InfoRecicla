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
        Schema::create('centros_acopio', function (Blueprint $table) {
            $table->char('id', 36)->default('uuid()')->primary();
            $table->string('nombre', 150);
            $table->enum('tipo', ['Planta', 'Proveedor', 'Otro'])->default('Otro')->index('idx_cac_tipo');
            $table->string('nit', 20)->nullable();
            $table->enum('alcance', ['global', 'eca'])->default('global')->index('idx_cac_alcance');
            $table->char('owner_punto_eca_id', 36)->nullable()->index('fk_cac_owner_eca');
            $table->string('contacto', 100)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email', 120)->nullable();
            $table->string('sitio_web', 200)->nullable();
            $table->string('horario_atencion', 150)->nullable();
            $table->string('direccion', 200)->nullable();
            $table->string('ciudad', 60)->nullable()->index('idx_cac_ciudad');
            $table->string('localidad', 60)->nullable()->index('idx_cac_localidad');
            $table->decimal('latitud', 10, 6)->nullable();
            $table->decimal('longitud', 10, 6)->nullable();
            $table->enum('estado', ['activo', 'inactivo', 'bloqueado'])->default('activo')->index('idx_cac_estado');
            $table->string('notas', 300)->nullable();
            $table->dateTime('creado')->useCurrent();
            $table->dateTime('actualizado')->useCurrentOnUpdate()->useCurrent();

            $table->unique(['nombre', 'tipo', 'alcance', 'owner_punto_eca_id'], 'uq_cac_scoped');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('centros_acopio');
    }
};
