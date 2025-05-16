<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExhibitionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('exhibitions')->insert([
            [
                'seller_id' => '1',
                'condition_id' => '1',
                'name' => '腕時計',
                'brand' => 'Armani',
                'price' => '15000',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'img_url' => 'storage/img/watch.jpg',
            ],
            [
                'seller_id' => '1',
                'condition_id' => '2',
                'name' => 'HDD',
                'brand' => '',
                'price' => '5000',
                'description' => '高速で信頼性の高いハードディスク',
                'img_url' => 'storage/img/HDD.jpg',
            ],
            [
                'seller_id' => '2',
                'condition_id' => '3',
                'name' => '玉ねぎ3束',
                'brand' => 'コーチ農園',
                'price' => '300',
                'description' => '新鮮な玉ねぎ3束のセット',
                'img_url' => 'storage/img/onions.jpg',
            ],
            [
                'seller_id' => '2',
                'condition_id' => '4',
                'name' => '革靴',
                'brand' => '',
                'price' => '4000',
                'description' => 'クラシックなデザインの革靴',
                'img_url' => 'storage/img/leather-shoes.jpg',
            ],
            [
                'seller_id' => '2',
                'condition_id' => '1',
                'name' => 'ノートPC',
                'brand' => '',
                'price' => '45000',
                'description' => '高性能なノートパソコン',
                'img_url' => 'storage/img/laptop.jpg',
            ],
            [
                'seller_id' => '1',
                'condition_id' => '2',
                'name' => 'マイク',
                'brand' => '',
                'price' => '8000',
                'description' => '高音質のレコーディング用マイク',
                'img_url' => 'storage/img/microphone.jpg',
            ],
            [
                'seller_id' => '2',
                'condition_id' => '3',
                'name' => 'ショルダーバッグ',
                'brand' => '',
                'price' => '3500',
                'description' => 'おしゃれなショルダーバッグ',
                'img_url' => 'storage/img/shoulder-bag.jpg',
            ],
            [
                'seller_id' => '1',
                'condition_id' => '4',
                'name' => 'タンブラー',
                'brand' => '',
                'price' => '500',
                'description' => '使いやすいタンブラー',
                'img_url' => 'storage/img/tumbler.jpg',
            ],
            [
                'seller_id' => '2',
                'condition_id' => '1',
                'name' => 'コーヒーミル',
                'brand' => '',
                'price' => '4000',
                'description' => '手動のコーヒーミル',
                'img_url' => 'storage/img/coffee-grinder.jpg',
            ],
            [
                'seller_id' => '2',
                'condition_id' => '2',
                'name' => 'メイクセット',
                'brand' => '',
                'price' => '2500',
                'description' => '便利なメイクアップセット',
                'img_url' => 'storage/img/makeup-set.jpg',
            ],
        ]);
    }
}
