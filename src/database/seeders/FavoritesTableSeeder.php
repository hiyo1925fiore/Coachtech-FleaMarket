<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FavoritesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('favorites')->insert([
            [
                'user_id' => '1',
                'exhibition_id' => '3',
            ],
            [
                'user_id' => '1',
                'exhibition_id' => '5',
            ],
            [
                'user_id' => '1',
                'exhibition_id' => '7',
            ],
            [
                'user_id' => '1',
                'exhibition_id' => '10',
            ],
            [
                'user_id' => '2',
                'exhibition_id' => '2',
            ],
            [
                'user_id' => '2',
                'exhibition_id' => '6',
            ],
        ]);
    }
}
