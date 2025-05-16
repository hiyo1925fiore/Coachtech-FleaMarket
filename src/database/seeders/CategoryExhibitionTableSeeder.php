<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryExhibitionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('category_exhibition')->insert([
            [
                'exhibition_id' => '1',
                'category_id' => '5',
            ],
            [
                'exhibition_id' => '2',
                'category_id' => '2',
            ],
            [
                'exhibition_id' => '3',
                'category_id' => '10',
            ],
            [
                'exhibition_id' => '4',
                'category_id' => '1',
            ],
            [
                'exhibition_id' => '4',
                'category_id' => '5',
            ],
            [
                'exhibition_id' => '5',
                'category_id' => '2',
            ],
            [
                'exhibition_id' => '6',
                'category_id' => '2',
            ],
            [
                'exhibition_id' => '7',
                'category_id' => '1',
            ],
            [
                'exhibition_id' => '7',
                'category_id' => '4',
            ],
            [
                'exhibition_id' => '8',
                'category_id' => '10',
            ],
            [
                'exhibition_id' => '9',
                'category_id' => '3',
            ],
            [
                'exhibition_id' => '9',
                'category_id' => '10',
            ],
            [
                'exhibition_id' => '10',
                'category_id' => '6',
            ],
        ]);
    }
}
