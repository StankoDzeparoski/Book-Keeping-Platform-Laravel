<?php

namespace Database\Factories;

use App\Models\EquipmentHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EquipmentHistory>
 */
class EquipmentHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $employeeIds = [];
        for ($i = 0; $i < fake()->numberBetween(1, 3); $i++) {
            $employeeIds[] = fake()->numberBetween(1, 50);
        }

        $loanDates = [];
        $loanExpireDates = [];
        for ($i = 0; $i < fake()->numberBetween(1, 4); $i++) {
            $loanDate = fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d');
            $loanDates[] = $loanDate;
            $loanExpireDates[] = fake()->dateTimeBetween($loanDate, '+1 year')->format('Y-m-d');
        }

        return [
            'equipment_id' => null, // Will be set in seeder
            'employee_ids' => $employeeIds,
            'loan_date' => $loanDates,
            'loan_expire_date' => $loanExpireDates,
        ];
    }
}
