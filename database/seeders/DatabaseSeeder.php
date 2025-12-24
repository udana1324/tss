<?php

namespace Database\Seeders;

use App\Models\Setting\ModuleAccess;
use Illuminate\Database\Seeder;
use App\Models\Setting\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
            $this->call([
                UserSeeder::class,
                ModuleSeeder::class,
                ModuleAccessSeeder::class,
            ]);
    }
}
