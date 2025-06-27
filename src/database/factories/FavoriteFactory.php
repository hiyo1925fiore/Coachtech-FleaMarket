<?php

namespace Database\Factories;

use App\Models\Favorite;
use App\Models\User;
use App\Models\Exhibition;
use Illuminate\Database\Eloquent\Factories\Factory;

class FavoriteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Favorite::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'exhibition_id' => Exhibition::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
