<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Cargo; // Asegúrate de importar el modelo Cargo
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buscamos el rol/cargo
        $superAdminCargo = Cargo::where('nombre', 'Super Administrador')->first();

        // 2. Validación de seguridad: Si no existe el cargo, avisamos y detenemos.
        if (!$superAdminCargo) {
            $this->command->error('Error: El cargo "Super Administrador" no existe.');
            $this->command->warn('Sugerencia: Ejecuta primero el CargoSeeder (php artisan db:seed --class=CargoSeeder).');
            return;
        }

        // 3. Crear el usuario (Usamos firstOrCreate para evitar duplicados si corres el seeder 2 veces)
        User::firstOrCreate(
            ['email' => 'admin@admin.com'], // Buscamos por este email
            [
                'name' => 'Super Administrador',
                'password' => Hash::make('password'), // Cambia 'password' por la contraseña que quieras
                'cargo_id' => $superAdminCargo->id,
                // Agrega aquí otros campos si tu tabla users los pide (ej: 'estatus' => 1)
            ]
        );
        
        $this->command->info('Usuario Super Administrador creado correctamente.');
    }
}