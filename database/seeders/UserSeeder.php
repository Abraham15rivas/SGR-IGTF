<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Admin
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@test.com',
            'password' => Hash::make('secret123'),
            'role_id' => 1
        ]);
        // Gerente 
        User::create([
            'name' => 'Gerente',
            'email' => 'gerente@test.com',
            'password' => Hash::make('secret123'),
            'role_id' => 2
        ]);
        // Analista
        User::create([
            'name' => 'Analista',
            'email' => 'analista@test.com',
            'password' => Hash::make('secret123'),
            'role_id' => 3
        ]);
    }
}
