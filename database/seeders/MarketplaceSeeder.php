<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarketplaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('marketplaces')->truncate();
        DB::table('marketplaces')->insert([
            [
                'name' => 'Tokopedia',
                'slug' => 'tokopedia'
            ],
            [
                'name' => 'Bukalapak',
                'slug' => 'bukalapak'
            ],
            [
                'name' => 'Shopee',
                'email' => 'shopee'
            ],
            [
                'name' => 'Lazada',
                'email' => 'lazada'
            ],
            [
                'name' => 'Blibli',
                'email' => 'blibli'
            ]
        ]);
    }
}
