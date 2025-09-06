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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->char('id', 36)->default('uuid()')->primary();
            $table->string('correo', 160)->unique('uq_usuarios_email');
            $table->string('password');
            $table->string('nombre', 50);
            $table->string('apellido', 50);
            $table->enum('rol', ['Ciudadano', 'GestorECA', 'Administrador']);
            $table->string('tipo_documento', ['Cédula de Ciudadanía', 'Cédula de Extranjería', 'Tarjeta de Identidad', 'Pasaporte'])->nullable();
            $table->string('numero_documento', 30)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->boolean('recibe_notificaciones')->default(true);
            $table->date('fecha_nacimiento')->nullable();
            $table->string('direccion', 200)->nullable();
            $table->string('avatar_url', 300)->nullable();
            $table->string('nombre_usuario', 60)->nullable()->unique('uq_usuarios_username');
            $table->enum('genero', ['Masculino', 'Femenino', 'Otro'])->nullable();
            $table->string('localidad', 60)->nullable();
            $table->enum('estado', ['activo', 'inactivo', 'bloqueado'])->default('activo');
            $table->dateTime('creado')->useCurrent();
            $table->dateTime('actualizado')->useCurrentOnUpdate()->useCurrent();

            $table->index(['tipo_documento', 'numero_documento'], 'idx_usuarios_doc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
