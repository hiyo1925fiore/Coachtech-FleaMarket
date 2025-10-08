<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'ユーザー1',
                'email' => 'hoge1@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'ユーザー2',
                'email' => 'hoge2@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'ユーザー3',
                'email' => 'hoge3@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        ]);

        DB::table('profiles')->insert([
            [
                'user_id' => 1,
                'img_url' => 'profile_images/マーガレット.jpg',
                'post_code' => '123-4567',
                'address' => '東京都目黒区下目黒2-20-28',
                'building' => 'いちご目黒ビル4階',
            ],
            [
                'user_id' => 2,
                'img_url' => '',
                'post_code' => '111-1111',
                'address' => '東京都千代田区1-1-1',
                'building' => ''
            ],
            [
                'user_id' => 3,
                'img_url' => 'profile_images/椿.jpg',
                'post_code' => '222-2222',
                'address' => '大阪府大阪市北区梅田2-2-2',
                'building' => 'COACHTECHマンション梅田 202号室'
            ],
        ]);
    }
}
