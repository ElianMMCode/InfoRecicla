<?php

// database/migrations/xxxx_xx_xx_xxxxxx_alter_usuarios_add_remember_token.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            if (!Schema::hasColumn('usuarios', 'remember_token')) {
                $table->rememberToken()->after('password'); // VARCHAR(100) NULL
            }
            // Opcional: verificación de correo en el futuro
            if (!Schema::hasColumn('usuarios', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('remember_token');
            }
        });
    }
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            if (Schema::hasColumn('usuarios', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
            if (Schema::hasColumn('usuarios', 'remember_token')) {
                $table->dropColumn('remember_token');
            }
        });
    }
};