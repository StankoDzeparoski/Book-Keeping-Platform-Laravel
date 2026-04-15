<?php

namespace Database\Factories;

use App\Models\MaintenanceRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaintenanceRecord>
 */
class MaintenanceRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $descriptions = [
            'Screen replacement',
            'Battery replacement',
            'Keyboard repair',
            'Software update',
            'Hard drive replacement',
            'Cleaning and maintenance',
            'Port repair',
            'Motherboard replacement',
            'Cooling system maintenance',
            'RAM upgrade',
        ];

        $maintenanceDates = [];
        for ($i = 0; $i < fake()->numberBetween(1, 4); $i++) {
            $maintenanceDates[] = fake()->dateTimeBetween('-3 years', 'now')->format('Y-m-d');
        }

        return [
            'equipment_id' => null, // Will be set in seeder
            'description' => [
                fake()->randomElement($descriptions),
                fake()->randomElement($descriptions),
            ],
            'cost' => fake()->numberBetween(50, 500),
            'maintenance_date' => $maintenanceDates,
        ];
    }
}
