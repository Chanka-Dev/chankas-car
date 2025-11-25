<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si ya existe un usuario admin
        if (!User::where('email', 'chankalopez@gmail.com')->exists()) {
            User::create([
                'name' => 'Chankas Car Admin',
                'email' => 'chankalopez@gmail.com',
                'password' => Hash::make('Antonio1967.'),
                'role' => 'admin',
                'is_active' => true,
            ]);
        }

        // Actualizar usuarios existentes para darles rol de admin
        User::where('email', 'chankalopez@gmail.com')->update([
            'role' => 'admin',
            'is_active' => true,
        ]);
    }
}