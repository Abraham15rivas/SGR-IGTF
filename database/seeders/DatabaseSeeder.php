<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(RifTypeSeeder::class);
        $this->call(BankSeeder::class);
        $this->call(ConceptDailySeeder::class);
        $this->call(InstrumentSeeder::class);
    }
}
