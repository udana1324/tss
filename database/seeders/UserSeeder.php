<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'id' => '1',
            'user_name' => 'sata',
            'user_password' => Hash::make('sata'),
            'user_group' => 'super_admin',
            'active' => 'Y',
            'action' => 'TAMBAH',
            'user' => 'sata',
            'created_by' => 'sata',
            'remember_token' => Str::random(10),
        ]);
    }
}
