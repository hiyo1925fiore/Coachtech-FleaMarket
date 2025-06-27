<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\User;
use App\Models\Exhibition;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

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
            'comment' => $this->faker->text(255),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
