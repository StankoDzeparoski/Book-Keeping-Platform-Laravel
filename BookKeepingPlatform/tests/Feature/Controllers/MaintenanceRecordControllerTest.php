<?php

namespace Tests\Feature\Controllers;

use App\Models\Equipment;
use App\Models\MaintenanceRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceRecordControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $manager;
    protected User $employee;
    protected Equipment $equipment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->manager = User::factory()->create(['role' => 'Manager']);
        $this->employee = User::factory()->create(['role' => 'Employee']);
        $this->equipment = Equipment::factory()->create();
    }

    /** @test */
    public function manager_can_view_maintenance_records_list(): void
    {
        MaintenanceRecord::factory()->create([
            'equipment_id' => $this->equipment->id,
        ]);

        $response = $this->actingAs($this->manager)
            ->get(route('maintenanceRecord.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function employee_cannot_view_maintenance_records(): void
    {
        $response = $this->actingAs($this->employee)
            ->get(route('maintenanceRecord.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function manager_can_view_create_form(): void
    {
        $response = $this->actingAs($this->manager)
            ->get(route('maintenanceRecord.create'));

        $response->assertStatus(200);
    }

    /** @test */
    public function employee_cannot_access_create_form(): void
    {
        $response = $this->actingAs($this->employee)
            ->get(route('maintenanceRecord.create'));

        $response->assertStatus(403);
    }

    /** @test */
    public function manager_can_create_maintenance_record(): void
    {
        $response = $this->actingAs($this->manager)
            ->post(route('maintenanceRecord.store'), [
                'equipment_id' => $this->equipment->id,
                'description' => ['Screen replaced'],
                'maintenance_date' => ['2026-04-15'],
                'cost' => 150,
            ]);

        $response->assertRedirect(route('maintenanceRecord.index'));
        $this->assertDatabaseHas('maintenance_records', [
            'equipment_id' => $this->equipment->id,
            'cost' => 150,
        ]);
    }

    /** @test */
    public function employee_cannot_create_maintenance_record(): void
    {
        $response = $this->actingAs($this->employee)
            ->post(route('maintenanceRecord.store'), [
                'equipment_id' => $this->equipment->id,
                'description' => ['Screen replaced'],
                'maintenance_date' => ['2026-04-16'],
                'cost' => 150,
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function manager_can_view_single_maintenance_record(): void
    {
        $record = MaintenanceRecord::factory()->create([
            'equipment_id' => $this->equipment->id,
        ]);

        $response = $this->actingAs($this->manager)
            ->get(route('maintenanceRecord.show', $record));

        $response->assertStatus(200);
    }

    /** @test */
    public function manager_can_view_edit_form(): void
    {
        $record = MaintenanceRecord::factory()->create([
            'equipment_id' => $this->equipment->id,
        ]);

        $response = $this->actingAs($this->manager)
            ->get(route('maintenanceRecord.edit', $record));

        $response->assertStatus(200);
    }

    /** @test */
    public function employee_cannot_view_edit_form(): void
    {
        $record = MaintenanceRecord::factory()->create([
            'equipment_id' => $this->equipment->id,
        ]);

        $response = $this->actingAs($this->employee)
            ->get(route('maintenanceRecord.edit', $record));

        $response->assertStatus(403);
    }

    /** @test */
    public function manager_can_update_maintenance_record(): void
    {
        $record = MaintenanceRecord::factory()->create([
            'equipment_id' => $this->equipment->id,
            'cost' => 100,
        ]);

        $response = $this->actingAs($this->manager)
            ->patch(route('maintenanceRecord.update', $record), [
                'equipment_id' => $this->equipment->id,
                'description' => ['Updated repair'],
                'maintenance_date' => ['2026-04-16'],
                'cost' => 200,
            ]);

        $response->assertRedirect(route('maintenanceRecord.show', $record));
        $this->assertDatabaseHas('maintenance_records', [
            'id' => $record->id,
            'cost' => 200,
        ]);
    }

    /** @test */
    public function employee_cannot_update_maintenance_record(): void
    {
        $record = MaintenanceRecord::factory()->create([
            'equipment_id' => $this->equipment->id,
        ]);

        $response = $this->actingAs($this->employee)
            ->patch(route('maintenanceRecord.update', $record), [
                'equipment_id' => $this->equipment->id,
                'description' => ['Updated'],
                'maintenance_date' => ['2026-04-16'],
                'cost' => 200,
            ]);



        $response->assertStatus(403);
    }

    /** @test */
    public function manager_can_delete_maintenance_record(): void
    {
        $record = MaintenanceRecord::factory()->create([
            'equipment_id' => $this->equipment->id,
        ]);

        $response = $this->actingAs($this->manager)
            ->delete(route('maintenanceRecord.destroy', $record));

        $response->assertRedirect(route('maintenanceRecord.index'));
        $this->assertModelMissing($record);
    }

    /** @test */
    public function employee_cannot_delete_maintenance_record(): void
    {
        $record = MaintenanceRecord::factory()->create([
            'equipment_id' => $this->equipment->id,
        ]);

        $response = $this->actingAs($this->employee)
            ->delete(route('maintenanceRecord.destroy', $record));

        $response->assertStatus(403);
    }

    /** @test */
    public function maintenance_record_contains_equipment_relationship(): void
    {
        $record = MaintenanceRecord::factory()->create([
            'equipment_id' => $this->equipment->id,
        ]);

        $this->assertNotNull($record->equipment);
        $this->assertEquals($this->equipment->id, $record->equipment->id);
    }

    /** @test */
    public function maintenance_records_can_be_searched(): void
    {
        $equipment1 = Equipment::factory()->create(['brand' => 'Dell']);
        $equipment2 = Equipment::factory()->create(['brand' => 'HP']);

        MaintenanceRecord::factory()->create(['equipment_id' => $equipment1->id]);
        MaintenanceRecord::factory()->create(['equipment_id' => $equipment2->id]);

        $response = $this->actingAs($this->manager)
            ->get(route('maintenanceRecord.index', ['search' => 'Dell']));

        $response->assertStatus(200);
    }

    /** @test */
    public function maintenance_record_tracks_cost_correctly(): void
    {
        $record = MaintenanceRecord::factory()->create([
            'equipment_id' => $this->equipment->id,
            'cost' => 500,
        ]);

        $this->assertEquals(500, $record->cost);
    }

    /** @test */
    public function maintenance_record_stores_descriptions_as_array(): void
    {
        $record = MaintenanceRecord::create([
            'equipment_id' => $this->equipment->id,
            'description' => ['Screen replaced', 'Battery replaced'],
            'maintenance_date' => ['2026-04-15', '2026-04-17'],
            'cost' => 250,
        ]);

        $this->assertIsArray($record->description);
        $this->assertCount(2, $record->description);
    }
}

