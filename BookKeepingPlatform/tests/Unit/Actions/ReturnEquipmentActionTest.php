<?php

namespace Tests\Unit\Actions;

use App\Actions\ReturnEquipmentAction;
use App\Enums\Status;
use App\Models\Equipment;
use App\Models\EquipmentHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReturnEquipmentActionTest extends TestCase
{
    use RefreshDatabase;

    protected ReturnEquipmentAction $action;
    protected Equipment $equipment;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new ReturnEquipmentAction();
        $this->user = User::factory()->create();

        // Create equipment in assigned state
        $this->equipment = Equipment::factory()->create([
            'status' => Status::ASSIGNED,
            'user_id' => $this->user->id,
            'loan_date' => Carbon::parse('2026-04-17'),
            'loan_expire_date' => Carbon::parse('2026-04-30'),
        ]);

        // Create history record
        EquipmentHistory::create([
            'equipment_id' => $this->equipment->id,
            'user_ids' => [$this->user->id],
            'loan_date' => ['2026-04-17'],
            'loan_expire_date' => ['2026-04-30'],
        ]);
    }

    /** @test */
    public function it_returns_equipment_today_and_sets_available(): void
    {
        $today = Carbon::now()->format('Y-m-d');

        $result = $this->action->execute($this->equipment, $today);

        $this->assertEquals(Status::AVAILABLE, $result->status);
        $this->assertNull($result->user_id);
        $this->assertNull($result->loan_date);
    }

    /** @test */
    public function it_returns_equipment_early_and_stays_assigned(): void
    {
        $result = $this->action->execute($this->equipment, '2026-04-20');

        $this->assertEquals(Status::ASSIGNED, $result->status);
        $this->assertEquals($this->user->id, $result->user_id);
        $this->assertNotNull($result->loan_date);
    }

    /** @test */
    public function it_updates_loan_expire_date_on_early_return(): void
    {
        $this->action->execute($this->equipment, '2026-04-20');

        $this->equipment->refresh();
        $this->assertEquals('2026-04-20', $this->equipment->loan_expire_date->format('Y-m-d'));
    }

    /** @test */
    public function it_updates_equipment_history_with_actual_return_date(): void
    {
        $this->action->execute($this->equipment, '2026-04-20');

        $history = EquipmentHistory::where('equipment_id', $this->equipment->id)->first();

        // Last element should be updated return date
        $loanExpireDates = $history->loan_expire_date;
        $lastReturnDate = $loanExpireDates[count($loanExpireDates) - 1];
        $this->assertEquals('2026-04-20', $lastReturnDate);
    }

    /** @test */
    public function it_handles_return_on_original_due_date(): void
    {
        $result = $this->action->execute($this->equipment, '2026-04-30');

        $this->assertEquals('2026-04-30', $result->loan_expire_date->format('Y-m-d'));
        $this->assertEquals(Status::AVAILABLE, $result->status);
    }

    /** @test */
    public function it_handles_future_return_date(): void
    {
        $result = $this->action->execute($this->equipment, '2026-05-10');

        // Should stay assigned
        $this->assertEquals(Status::ASSIGNED, $result->status);
        $this->assertEquals($this->user->id, $result->user_id);

        // But expiration date should be updated
        $this->assertEquals('2026-05-10', $result->loan_expire_date->format('Y-m-d'));
    }
}

