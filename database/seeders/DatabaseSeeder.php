<?php

namespace Database\Seeders;

// se eliminó App\Models\User; usar Usuario si se desea poblar
use App\Models\Usuario;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ejemplo: crear un usuario de prueba mínimo si la tabla está vacía
        if (Usuario::count() === 0) {
            Usuario::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'correo' => 'admin@example.com',
                'password' => 'password', // mutator hace hash
                'nombre' => 'Admin',
                'apellido' => 'Inicial',
                'rol' => 'Administrador',
                'tipo_documento' => 'Cédula de Ciudadanía',
                'numero_documento' => '0000000000',
                'telefono' => '0000000',
                'recibe_notificaciones' => true,
                'fecha_nacimiento' => now()->subYears(30),
                'nombre_usuario' => 'admin',
                'estado' => 'activo',
            ]);
        }
    }
}
