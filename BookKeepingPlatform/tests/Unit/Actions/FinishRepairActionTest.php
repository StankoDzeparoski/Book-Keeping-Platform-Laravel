<?php

namespace Tests\Unit\Actions;

use App\Actions\FinishRepairAction;
use App\Enums\Status;
use App\Enums\Condition;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinishRepairActionTest extends TestCase
{
    use RefreshDatabase;

    protected FinishRepairAction $action;
    protected Equipment $equipment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new FinishRepairAction();
    }

    /** @test */
    public function it_changes_equipment_status_to_available_when_not_assigned(): void
    {
        $equipment = Equipment::factory()->create([
            'status' => Status::REPAIR,
            'user_id' => null,
        ]);

        $result = $this->action->execute($equipment);

        $this->assertEquals(Status::AVAILABLE, $result->status);
    }

    /** @test */
    public function it_changes_equipment_status_to_assigned_when_assigned(): void
    {
        $user = User::factory()->create();
        $equipment = Equipment::factory()->create([
            'status' => Status::REPAIR,
            'user_id' => $user->id,
        ]);

        $result = $this->action->execute($equipment);

        $this->assertEquals(Status::ASSIGNED, $result->status);
        $this->assertEquals($user->id, $result->user_id);
    }

    /** @test */
    public function it_updates_condition_to_used(): void
    {
        $equipment = Equipment::factory()->create([
            'status' => Status::REPAIR,
            'condition' => Condition::BROKEN,
        ]);

        $result = $this->action->execute($equipment);

        $this->assertEquals(Condition::USED, $result->condition);
    }

    /** @test */
    public function it_updates_equipment_with_user_assignment(): void
    {
        $user = User::factory()->create();
        $equipment = Equipment::factory()->create([
            'status' => Status::REPAIR,
            'user_id' => $user->id,
            'condition' => Condition::BROKEN,
        ]);

        $result = $this->action->execute($equipment);

        $this->assertEquals(Status::ASSIGNED, $result->status);
        $this->assertEquals(Condition::USED, $result->condition);
        $this->assertEquals($user->id, $result->user_id);
    }

    /** @test */
    public function it_persists_changes_to_database(): void
    {
        $equipment = Equipment::factory()->create([
            'status' => Status::REPAIR,
            'user_id' => null,
            'condition' => Condition::BROKEN,
        ]);

        $this->action->execute($equipment);

        $refreshed = Equipment::find($equipment->id);

        $this->assertEquals(Status::AVAILABLE, $refreshed->status);
        $this->assertEquals(Condition::USED, $refreshed->condition);
    }
}

