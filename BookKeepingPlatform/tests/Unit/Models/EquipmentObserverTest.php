<?php

namespace Tests\Unit\Models;

use App\Enums\Status;
use App\Enums\Condition;
use App\Models\Equipment;
use App\Models\EquipmentHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EquipmentObserverTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function new_equipment_has_available_status(): void
    {
        $equipment = Equipment::factory()->create();

        $this->assertEquals(Status::AVAILABLE, $equipment->status);
    }

    /** @test */
    public function equipment_history_created_when_loaned(): void
    {
        $user = User::factory()->create();
        $equipment = Equipment::factory()->create([
            'status' => Status::AVAILABLE,
        ]);

        // Simulate loan
        $equipment->forceFill([
            'user_id' => $user->id,
            'loan_date' => now(),
            'loan_expire_date' => now()->addDays(10),
            'status' => Status::ASSIGNED,
        ])->save();

        $history = EquipmentHistory::where('equipment_id', $equipment->id)->first();

        $this->assertNotNull($history);
        $this->assertContains($user->id, $history->user_ids);
    }

    /** @test */
    public function equipment_history_tracks_multiple_users(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $equipment = Equipment::factory()->create([
            'status' => Status::AVAILABLE,
        ]);

        // First loan
        $equipment->forceFill([
            'user_id' => $user1->id,
            'loan_date' => now(),
            'loan_expire_date' => now()->addDays(10),
            'status' => Status::ASSIGNED,
        ])->save();

        $equipment->refresh();

        // Second loan
        $equipment->forceFill([
            'user_id' => $user2->id,
            'loan_date' => now()->addDays(11),
            'loan_expire_date' => now()->addDays(20),
            'status' => Status::ASSIGNED,
        ])->save();

        $history = EquipmentHistory::where('equipment_id', $equipment->id)->first();

        $this->assertCount(2, $history->user_ids);
        $this->assertContains($user1->id, $history->user_ids);
        $this->assertContains($user2->id, $history->user_ids);
    }

    /** @test */
    public function equipment_model_has_relationships(): void
    {
        $user = User::factory()->create();
        $equipment = Equipment::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertNotNull($equipment->user());
        $this->assertNotNull($equipment->history());
        $this->assertNotNull($equipment->maintenanceRecords());
    }

    /** @test */
    public function equipment_casts_dates_correctly(): void
    {
        $equipment = Equipment::factory()->create([
            'acquisition_date' => '2026-04-15',
            'loan_date' => '2026-04-17',
            'loan_expire_date' => '2026-04-30',
        ]);

        $this->assertIsObject($equipment->acquisition_date);
        $this->assertIsObject($equipment->loan_date);
        $this->assertIsObject($equipment->loan_expire_date);
    }

    /** @test */
    public function equipment_casts_enums_correctly(): void
    {
        $equipment = Equipment::factory()->create([
            'condition' => 'new',
            'status' => Status::AVAILABLE,
        ]);

        $this->assertEquals(Condition::NEW, $equipment->condition);
        $this->assertEquals(Status::AVAILABLE, $equipment->status);
    }
}

