<?php

namespace Database\Seeders;

use App\Actions\LoanEquipmentAction;
use App\Models\User;
use App\Models\Equipment;
use App\Models\MaintenanceRecord;
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
            'name' => 'Test',
            'surname' => 'User',
            'email' => 'test@example.com',
        ]);

        // Create 50 users (employees)
        $users = User::factory(50)->create();

        // Create 100 equipment items (all with status AVAILABLE and no loan dates)
        $equipmentItems = Equipment::factory(100)->create();

        // Assign some equipment to users via Loan Action (simulating equipment loans)
        $loanAction = new \App\Actions\LoanEquipmentAction();
        foreach ($equipmentItems->random(30) as $equipment) {
            $user = $users->random();
            $loanDate = fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d');
            $loanExpireDate = fake()->dateTimeBetween($loanDate, '+1 year')->format('Y-m-d');

            $loanAction->execute($equipment, $user, $loanDate, $loanExpireDate);
        }

        // Create maintenance records for equipment
        foreach ($equipmentItems as $equipment) {
            MaintenanceRecord::factory(fake()->numberBetween(1, 3))->create([
                'equipment_id' => $equipment->id,
            ]);
        }
    }
}
