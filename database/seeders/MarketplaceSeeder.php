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
                'slug' => 'tokopedia',
                'picture' => config('app.url').'/assets/images/tokopedia.jpg'
            ],
            [
                'name' => 'Bukalapak',
                'slug' => 'bukalapak',
                'picture' => config('app.url').'/assets/images/bukalapak.png'
            ],
            [
                'name' => 'Shopee',
                'slug' => 'shopee',
                'picture' => config('app.url').'/assets/images/shopee.jpg'
            ],
            [
                'name' => 'Lazada',
                'slug' => 'lazada',
                'picture' => config('app.url').'/assets/images/lazada.png'
            ],
            [
                'name' => 'Blibli',
                'slug' => 'blibli',
                'picture' => config('app.url').'/assets/images/blibli.png'
            ]
        ]);
    }
}
