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
            'name' => 'Liliana',
            'email' => 'liliana.guerra@bav.com.ve',
            'password' => Hash::make('secret123'),
            'role_id' => 1
        ]);
        // Gerente 
        User::create([
            'name' => 'Juan',
            'email' => 'juan.rodriguez@bav.com.ve',
            'password' => Hash::make('secret123'),
            'role_id' => 2
        ]);
        // Analista
        User::create([
            'name' => 'Manuel',
            'email' => 'manuel.silva@bav.com.ve',
            'password' => Hash::make('secret123'),
            'role_id' => 3
        ]);
        // Analista
        User::create([
            'name' => 'Dayana',
            'email' => 'dayana.perozo@bav.com.ve',
            'password' => Hash::make('secret123'),
            'role_id' => 3
        ]);
    }
}
