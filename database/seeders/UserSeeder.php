<?php

namespace Database\Seeders;

use Carbon\Carbon;
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
        $now = Carbon::now('Asia/Jakarta')->toDateTimeString();
        DB::table('users')->truncate();
        DB::table('users')->insert([
            [
                'name' => 'system',
                'email' => 'system@fortamil.com',
                'password' => Hash::make('password'),
                'role_id' => 4,
                'email_verified_at' => $now,
                'warehouse_id' => null
            ],
            [
                'name' => 'admin',
                'email' => 'admin@fortamil.com',
                'password' => Hash::make('password'),
                'role_id' => 1,
                'email_verified_at' => $now,
                'warehouse_id' => 1
            ],
            [
                'name' => 'admin2',
                'email' => 'admin2@fortamil.com',
                'password' => Hash::make('password'),
                'role_id' => 1,
                'email_verified_at' => $now,
                'warehouse_id' => 2
            ],
            [
                'name' => 'admin3',
                'email' => 'admin3@fortamil.com',
                'password' => Hash::make('password'),
                'role_id' => 1,
                'email_verified_at' => $now,
                'warehouse_id' => 3
            ],
            [
                'name' => 'warehouse',
                'email' => 'warehouse@fortamil.com',
                'password' => Hash::make('password'),
                'role_id' => 2,
                'email_verified_at' => $now,
                'warehouse_id' => 1
            ],
            [
                'name' => 'warehouse2',
                'email' => 'warehouse2@fortamil.com',
                'password' => Hash::make('password'),
                'role_id' => 2,
                'email_verified_at' => $now,
                'warehouse_id' => 2
            ],
            [
                'name' => 'warehouse3',
                'email' => 'warehouse3@fortamil.com',
                'password' => Hash::make('password'),
                'role_id' => 2,
                'email_verified_at' => $now,
                'warehouse_id' => 3
            ],
            [
                'name' => 'dropshipper',
                'email' => 'dropshipper@fortamil.com',
                'password' => Hash::make('password'),
                'role_id' => 3,
                'email_verified_at' => $now,
                'warehouse_id' => null
            ],
            [
                'name' => 'dropshipper2',
                'email' => 'dropshipper2@fortamil.com',
                'password' => Hash::make('password'),
                'role_id' => 3,
                'email_verified_at' => $now,
                'warehouse_id' => null
            ],
            [
                'name' => 'dropshipper3',
                'email' => 'dropshipper3@fortamil.com',
                'password' => Hash::make('password'),
                'role_id' => 3,
                'email_verified_at' => $now,
                'warehouse_id' => null
            ],
            [
                'name' => 'super admin',
                'email' => 'sa@fortamil.com',
                'password' => Hash::make('password'),
                'role_id' => 4,
                'email_verified_at' => $now,
                'warehouse_id' => null
            ],
            [
                'name' => 'cashier',
                'email' => 'cashier@fortamil.com',
                'password' => Hash::make('password'),
                'role_id' => 5,
                'email_verified_at' => $now,
                'warehouse_id' => 1
            ],
            [
                'name' => 'cashier2',
                'email' => 'cashier2@fortamil.com',
                'password' => Hash::make('password'),
                'role_id' => 5,
                'email_verified_at' => $now,
                'warehouse_id' => 2
            ],
            [
                'name' => 'cashier3',
                'email' => 'cashier3@fortamil.com',
                'password' => Hash::make('password'),
                'role_id' => 5,
                'email_verified_at' => $now,
                'warehouse_id' => 3
            ]
        ]);
    }
}
