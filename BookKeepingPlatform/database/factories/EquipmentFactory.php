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
            'status' => fake()->randomElement(Status::cases()),
            'acquisition_date' => fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            'loan_date' => fake()->optional(0.5)->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'loan_expire_date' => fake()->optional(0.5)->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'storage_location' => fake()->randomElement(['Warehouse A', 'Warehouse B', 'Office', 'Storage Room', 'Lab']),
            'employee_id' => null,
        ];
    }
}
