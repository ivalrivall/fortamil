<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->truncate();
        Category::factory()->count(10)->create();
        // DB::table('categories')->insert([
        //     [
        //         'name' => 'Kesehatan',
        //         'slug' => 'kesehatan',
        //         'picture' => $faker->imageUrl(360, 360, 'animals', true, 'cats')
        //     ],
        //     [
        //         'name' => 'Olahraga',
        //         'slug' => 'olahraga',
        //         'picture' => $faker->imageUrl(360, 360, 'animals', true, 'cats')
        //     ],
        //     [
        //         'name' => 'Properti',
        //         'slug' => 'properti',
        //         'picture' => $faker->imageUrl(360, 360, 'animals', true, 'cats')
        //     ],
        //     [
        //         'name' => 'Otomotif',
        //         'slug' => 'otomotif',
        //         'picture' => $faker->imageUrl(360, 360, 'animals', true, 'cats')
        //     ],
        //     [
        //         'name' => 'Buku',
        //         'slug' => 'buku',
        //         'picture' => $faker->imageUrl(360, 360, 'animals', true, 'cats')
        //     ],
        //     [
        //         'name' => 'Dapur',
        //         'slug' => 'dapur',
        //         'picture' => $faker->imageUrl(360, 360, 'animals', true, 'cats')
        //     ],
        //     [
        //         'name' => 'Handphone & Tablet',
        //         'slug' => 'hp-tablet',
        //         'picture' => $faker->imageUrl(360, 360, 'animals', true, 'cats')
        //     ],
        //     [
        //         'name' => 'Fashion Pria',
        //         'slug' => 'fashion-pria',
        //         'picture' => $faker->imageUrl(360, 360, 'animals', true, 'cats')
        //     ],
        //     [
        //         'name' => 'Office & Stationery',
        //         'slug' => 'office-stationery',
        //         'picture' => $faker->imageUrl(360, 360, 'animals', true, 'cats')
        //     ]
        // ]);
    }
}
