<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_methods')->truncate();
        DB::table('payment_methods')->insert([
            [
                'name' => 'Tempo',
                'slug' => 'tempo'
            ],
            [
                'name' => 'Transfer',
                'slug' => 'transfer'
            ],
            [
                'name' => 'Cash',
                'email' => 'cash'
            ]
        ]);
    }
}
