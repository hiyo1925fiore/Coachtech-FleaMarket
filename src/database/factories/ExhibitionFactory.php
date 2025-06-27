<?php

namespace Database\Factories;

use App\Models\Exhibition;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExhibitionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Exhibition::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        // img/で始まるファイルパスの配列
        $imageFiles = [
            'img/sample1.jpg',
            'img/sample2.jpg',
            'img/sample3.png',
            'img/sample4.jpg',
            'img/sample5.png',
            'img/fashion1.jpg',
            'img/fashion2.jpg',
            'img/accessory1.jpg',
            'img/accessory2.png',
            'img/electronics1.jpg',
            'img/electronics2.jpg',
            'img/book1.jpg',
            'img/furniture1.jpg',
            'img/toy1.jpg',
            'img/sports1.jpg',
        ];

        return [
            'seller_id' => User::factory(),
            'name' => $this->faker->words(3, true),
            'brand' => $this->faker->company(),
            'price' => $this->faker->numberBetween(1, 10000),
            'condition_id' => $this->faker->numberBetween(1, 4), // 1: 良好, 2: 目立った傷や汚れなし, 3: やや傷や汚れあり, 4: 状態が悪い
            'description' => $this->faker->text(255),
            'img_url' => $this->faker->randomElement($imageFiles),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * 特定のユーザーの出品
     */
    public function forUser(User $user)
    {
        return $this->state(fn (array $attributes) => [
            'seller_id' => $user->id,
        ]);
    }
}
