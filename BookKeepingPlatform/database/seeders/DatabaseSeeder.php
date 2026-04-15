<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use App\Models\Equipment;
use App\Models\MaintenanceRecord;
use App\Models\EquipmentHistory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create 50 employees
        $employees = Employee::factory(50)->create();

        // Create 100 equipment items
        $equipmentItems = Equipment::factory(100)->create();

        // Assign some equipment to employees
        foreach ($equipmentItems->random(50) as $equipment) {
            $equipment->update([
                'employee_id' => $employees->random()->id,
            ]);
        }

        // Create maintenance records for equipment
        foreach ($equipmentItems as $equipment) {
            MaintenanceRecord::factory(fake()->numberBetween(1, 3))->create([
                'equipment_id' => $equipment->id,
            ]);
        }

        // Create equipment history records
        foreach ($equipmentItems as $equipment) {
            EquipmentHistory::factory(fake()->numberBetween(1, 2))->create([
                'equipment_id' => $equipment->id,
            ]);
        }
    }
}
