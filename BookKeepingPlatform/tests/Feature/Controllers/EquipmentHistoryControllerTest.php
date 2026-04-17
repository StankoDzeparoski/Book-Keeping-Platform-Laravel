<?php

namespace Tests\Feature\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EquipmentHistoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $manager;
    protected User $employee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->manager = User::factory()->create(['role' => 'Manager']);
        $this->employee = User::factory()->create(['role' => 'Employee']);
    }

    /** @test */
    public function manager_can_view_equipment_history_list(): void
    {
        $equipment = Equipment::factory()->create();
        $history = EquipmentHistory::factory()->create([
            'equipment_id' => $equipment->id,
        ]);

        $response = $this->actingAs($this->manager)
            ->get(route('equipmentHistory.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function employee_cannot_access_equipment_history(): void
    {
        $response = $this->actingAs($this->employee)
            ->get(route('equipmentHistory.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function manager_can_view_single_history_record(): void
    {
        $equipment = Equipment::factory()->create();
        $history = EquipmentHistory::factory()->create([
            'equipment_id' => $equipment->id,
        ]);

        $response = $this->actingAs($this->manager)
            ->get(route('equipmentHistory.show', $history));

        $response->assertStatus(200);
    }

    /** @test */
    public function unauthenticated_user_cannot_view_history(): void
    {
        $equipment = Equipment::factory()->create();
        $history = EquipmentHistory::factory()->create([
            'equipment_id' => $equipment->id,
        ]);

        $response = $this->get(route('equipmentHistory.show', $history));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function equipment_history_shows_correct_data(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $equipment = Equipment::factory()->create();

        $history = EquipmentHistory::create([
            'equipment_id' => $equipment->id,
            'user_ids' => [$user1->id, $user2->id],
            'loan_date' => ['2026-04-17', '2026-05-01'],
            'loan_expire_date' => ['2026-04-30', '2026-05-15'],
        ]);

        $response = $this->actingAs($this->manager)
            ->get(route('equipmentHistory.show', $history));

        $response->assertStatus(200);
        $response->assertSee($equipment->brand);
    }

    /** @test */
    public function history_list_can_be_searched_by_brand(): void
    {
        $equipment1 = Equipment::factory()->create(['brand' => 'Dell']);
        $equipment2 = Equipment::factory()->create(['brand' => 'HP']);

        EquipmentHistory::factory()->create(['equipment_id' => $equipment1->id]);
        EquipmentHistory::factory()->create(['equipment_id' => $equipment2->id]);

        $response = $this->actingAs($this->manager)
            ->get(route('equipmentHistory.index', ['search' => 'Dell']));

        $response->assertStatus(200);
    }

    /** @test */
    public function history_contains_equipment_relationship(): void
    {
        $equipment = Equipment::factory()->create();
        $history = EquipmentHistory::factory()->create([
            'equipment_id' => $equipment->id,
        ]);

        $response = $this->actingAs($this->manager)
            ->get(route('equipmentHistory.show', $history));

        $response->assertStatus(200);
        $this->assertNotNull($history->equipment);
    }

    /** @test */
    public function history_stores_multiple_loan_records(): void
    {
        $equipment = Equipment::factory()->create();

        $history = EquipmentHistory::create([
            'equipment_id' => $equipment->id,
            'user_ids' => [1, 2, 1],
            'loan_date' => ['2026-04-17', '2026-05-01', '2026-05-20'],
            'loan_expire_date' => ['2026-04-30', '2026-05-15', '2026-06-01'],
        ]);

        $this->assertCount(3, $history->user_ids);
        $this->assertCount(3, $history->loan_date);
        $this->assertCount(3, $history->loan_expire_date);
    }

    /** @test */
    public function history_json_fields_are_arrays(): void
    {
        $equipment = Equipment::factory()->create();

        $history = EquipmentHistory::create([
            'equipment_id' => $equipment->id,
            'user_ids' => [1, 2],
            'loan_date' => ['2026-04-17', '2026-05-01'],
            'loan_expire_date' => ['2026-04-30', '2026-05-15'],
        ]);

        $this->assertIsArray($history->user_ids);
        $this->assertIsArray($history->loan_date);
        $this->assertIsArray($history->loan_expire_date);
    }
}

