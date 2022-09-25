<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->truncate();
        DB::table('users')->insert([
            [
                'name' => 'admin',
                'email' => 'admin@fortamil.com',
                'password' => Hash::make('password'),
                'role_id' => 1,
            ],
            [
                'name' => 'warehouse',
                'email' => 'warehouse@fortamil.com',
                'password' => Hash::make('password'),
                'role_id' => 2,
            ],
            [
                'name' => 'dropshipper',
                'email' => 'dropshipper@fortamil.com',
                'password' => Hash::make('password'),
                'role_id' => 3,
            ],
            [
                'name' => 'super admin',
                'email' => 'sa@fortamil.com',
                'password' => Hash::make('password'),
                'role_id' => 4,
            ],
            [
                'name' => 'cashier',
                'email' => 'cashier@fortamil.com',
                'password' => Hash::make('password'),
                'role_id' => 5,
            ]
        ]);
    }
}
