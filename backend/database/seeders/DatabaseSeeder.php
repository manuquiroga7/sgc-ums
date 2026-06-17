<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Usuario de prueba para el login simple del scaffold.
        User::updateOrCreate(
            ['email' => 'admin@sgc-ums.com'],
            [
                'name' => 'Administrador UMS',
                'password' => Hash::make('password'),
            ]
        );
    }
}
