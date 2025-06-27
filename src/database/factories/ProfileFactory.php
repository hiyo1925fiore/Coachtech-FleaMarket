<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Profile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        // profile_images/で始まるファイルパスの配列
        $imageFiles = [
            'profile_images/sample1.jpg',
            'profile_images/sample2.jpg',
            'profile_images/sample3.png',
            'profile_images/sample4.jpg',
            'profile_images/sample5.png',
            'profile_images/fashion1.jpg',
            'profile_images/fashion2.jpg',
            'profile_images/accessory1.jpg',
            'profile_images/accessory2.png',
            'profile_images/electronics1.jpg',
            'profile_images/electronics2.jpg',
            'profile_images/book1.jpg',
            'profile_images/furniture1.jpg',
            'profile_images/toy1.jpg',
            'profile_images/sports1.jpg',
        ];

        return [
            'user_id' => User::factory(),
            'img_url' => $this->faker->randomElement($imageFiles),
            'post_code' => $this->faker->regexify('[0-9]{3}-[0-9]{4}'),
            'address' => $this->faker->prefecture() . $this->faker->city() . $this->faker->streetAddress(),
            'building' => $this->faker->optional()->secondaryAddress(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
