<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->truncate();
        DB::table('roles')->insert([
            [
                'name' => 'Admin',
                'slug' => 'admin'
            ],
            [
                'name' => 'Warehouse Officer',
                'slug' => 'warehouse_officer'
            ],
            [
                'name' => 'Dropshipper',
                'slug' => 'dropshipper'
            ],
            [
                'name' => 'Super Admin',
                'slug' => 'super_admin'
            ],
            [
                'name' => 'Cashier',
                'slug' => 'cashier'
            ]
        ]);
    }
}
