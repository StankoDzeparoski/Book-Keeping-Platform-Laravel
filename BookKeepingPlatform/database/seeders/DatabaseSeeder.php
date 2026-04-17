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
            'role' => 'Employee',
        ]);

        // Create 50 users (mix of managers and employees)
        $users = User::factory(50)->create();

        // Create 100 equipment items (all with status AVAILABLE and no loan dates)
        // Conditions will only be NEW or USED (not BROKEN)
        $equipmentItems = Equipment::factory(100)->create();

        // Assign some equipment to users via Loan Action (simulating equipment loans)
        $loanAction = new \App\Actions\LoanEquipmentAction();
        foreach ($equipmentItems->random(30) as $equipment) {
            $user = $users->random();
            $loanDate = fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d');
            $loanExpireDate = fake()->dateTimeBetween($loanDate, '+1 year')->format('Y-m-d');

            $loanAction->execute($equipment, $user, $loanDate, $loanExpireDate);
        }

        // Create ONE maintenance record per equipment (no duplicates)
        // Only create for 20% of equipment to keep data reasonable
        foreach ($equipmentItems->random((int) count($equipmentItems) * 0.2) as $equipment) {
            MaintenanceRecord::factory()->create([
                'equipment_id' => $equipment->id,
            ]);
        }
    }
}
