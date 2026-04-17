<?php

namespace Tests\Unit\Actions;

use App\Actions\RepairEquipmentAction;
use App\Enums\Status;
use App\Enums\Condition;
use App\Models\Equipment;
use App\Models\MaintenanceRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepairEquipmentActionTest extends TestCase
{
    use RefreshDatabase;

    protected RepairEquipmentAction $action;
    protected Equipment $equipment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new RepairEquipmentAction();
        $this->equipment = Equipment::factory()->create([
            'condition' => Condition::BROKEN,
            'status' => Status::AVAILABLE,
        ]);
    }

    /** @test */
    public function it_changes_equipment_status_to_repair(): void
    {
        $this->action->execute(
            $this->equipment,
            'Screen replaced',
            150,
            '2026-04-15'
        );

        $this->equipment->refresh();
        $this->assertEquals(Status::REPAIR, $this->equipment->status);
    }

    /** @test */
    public function it_creates_new_maintenance_record(): void
    {
        $this->action->execute(
            $this->equipment,
            'Screen replaced',
            150,
            '2026-04-15'
        );

        $record = MaintenanceRecord::where('equipment_id', $this->equipment->id)->first();

        $this->assertNotNull($record);
        $this->assertContains('Screen replaced', $record->description);
        $this->assertContains('2026-04-15', $record->maintenance_date);
        $this->assertEquals(150, $record->cost);
    }

    /** @test */
    public function it_updates_existing_maintenance_record(): void
    {
        // First repair
        $this->action->execute(
            $this->equipment,
            'Screen replaced',
            150,
            '2026-04-15'
        );

        // Equipment needs to be refreshed and status reset for second repair
        $this->equipment->refresh();

        // Second repair
        $this->action->execute(
            $this->equipment,
            'Battery replaced',
            100,
            '2026-04-17'
        );

        $record = MaintenanceRecord::where('equipment_id', $this->equipment->id)->first();

        // Should have both repairs
        $this->assertCount(2, $record->description);
        $this->assertContains('Screen replaced', $record->description);
        $this->assertContains('Battery replaced', $record->description);

        // Cost should be cumulative
        $this->assertEquals(250, $record->cost);
    }

    /** @test */
    public function it_appends_to_maintenance_dates(): void
    {
        // First repair
        $this->action->execute(
            $this->equipment,
            'Screen replaced',
            150,
            '2026-04-15'
        );

        $this->equipment->refresh();

        // Second repair
        $this->action->execute(
            $this->equipment,
            'Battery replaced',
            100,
            '2026-04-17'
        );

        $record = MaintenanceRecord::where('equipment_id', $this->equipment->id)->first();

        $this->assertCount(2, $record->maintenance_date);
        $this->assertEquals(['2026-04-15', '2026-04-17'], $record->maintenance_date);
    }

    /** @test */
    public function it_returns_maintenance_record(): void
    {
        $record = $this->action->execute(
            $this->equipment,
            'Screen replaced',
            150,
            '2026-04-15'
        );

        $this->assertInstanceOf(MaintenanceRecord::class, $record);
        $this->assertEquals($this->equipment->id, $record->equipment_id);
    }
}

