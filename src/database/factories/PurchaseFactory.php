<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\User;
use App\Models\Exhibition;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Purchase::class;

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
            'payment' => $this->faker->numberBetween(1, 2), // 1: コンビニ支払い, 2: カード支払い
            'post_code' => $this->faker->regexify('[0-9]{3}-[0-9]{4}'),
            'address' => $this->faker->prefecture() . $this->faker->city() . $this->faker->streetAddress(),
            'building' => $this->faker->optional()->secondaryAddress(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * 特定のユーザーでの購入データを作成
     */
    public function forUser(User $user)
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * 特定の出品でのデータを作成
     */
    public function forExhibition(Exhibition $exhibition)
    {
        return $this->state(fn (array $attributes) => [
            'exhibition_id' => $exhibition->id,
        ]);
    }

    /**
     * コンビニ払い
     */
    public function convenience()
    {
        return $this->state(fn (array $attributes) => [
            'payment' => 1,
        ]);
    }

    /**
     * カード支払い
     */
    public function creditCard()
    {
        return $this->state(fn (array $attributes) => [
            'payment' => 2,
        ]);
    }
}
