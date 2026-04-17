<?php

namespace Database\Factories;

use App\Enums\Category;
use App\Enums\Condition;
use App\Enums\Status;
use App\Models\Equipment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Equipment>
 */
class EquipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'brand' => fake()->randomElement(['Dell', 'HP', 'Lenovo', 'Apple', 'ASUS', 'Logitech', 'Microsoft', 'Razer']),
            'model' => fake()->bothify('Model-###-???'),
            'category' => fake()->randomElement(Category::cases()),
            'cost' => fake()->numberBetween(200, 3000),
            'condition' => fake()->randomElement(Condition::cases()),
            'status' => Status::AVAILABLE, // Explicitly set default status
            'acquisition_date' => fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            'loan_date' => null,
            'loan_expire_date' => null,
            'storage_location' => fake()->randomElement(['Warehouse A', 'Warehouse B', 'Office', 'Storage Room', 'Lab']),
            'user_id' => null,
        ];
    }
}
