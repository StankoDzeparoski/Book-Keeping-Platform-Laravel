<?php

namespace Tests\Unit\Actions;

use App\Actions\LoanEquipmentAction;
use App\Enums\Status;
use App\Models\Equipment;
use App\Models\EquipmentHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanEquipmentActionTest extends TestCase
{
    use RefreshDatabase;

    protected LoanEquipmentAction $action;
    protected Equipment $equipment;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new LoanEquipmentAction();
        $this->equipment = Equipment::factory()->create([
            'status' => Status::AVAILABLE,
        ]);
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_loans_equipment_to_a_user(): void
    {
        $result = $this->action->execute(
            $this->equipment,
            $this->user,
            '2026-04-17',
            '2026-04-30'
        );

        $this->assertEquals(Status::ASSIGNED, $result->status);
        $this->assertEquals($this->user->id, $result->user_id);
        $this->assertNotNull($result->loan_date);
        $this->assertNotNull($result->loan_expire_date);
    }

    /** @test */
    public function it_creates_equipment_history_on_loan(): void
    {
        $this->action->execute(
            $this->equipment,
            $this->user,
            '2026-04-17',
            '2026-04-30'
        );

        $history = EquipmentHistory::where('equipment_id', $this->equipment->id)->first();

        $this->assertNotNull($history);
        $this->assertContains($this->user->id, $history->user_ids);
        $this->assertContains('2026-04-17', $history->loan_date);
        $this->assertContains('2026-04-30', $history->loan_expire_date);
    }

    /** @test */
    public function it_updates_existing_history_on_subsequent_loan(): void
    {
        $anotherUser = User::factory()->create();

        // First loan
        $this->action->execute(
            $this->equipment,
            $this->user,
            '2026-04-17',
            '2026-04-30'
        );

        // Refresh equipment status to AVAILABLE for second loan
        $this->equipment->forceFill(['status' => Status::AVAILABLE, 'user_id' => null])->saveQuietly();

        // Second loan
        $this->action->execute(
            $this->equipment,
            $anotherUser,
            '2026-05-01',
            '2026-05-10'
        );

        $history = EquipmentHistory::where('equipment_id', $this->equipment->id)->first();

        // Should have both users
        $this->assertCount(2, $history->user_ids);
        $this->assertContains($this->user->id, $history->user_ids);
        $this->assertContains($anotherUser->id, $history->user_ids);
    }

    /** @test */
    public function it_sets_correct_loan_dates(): void
    {
        $result = $this->action->execute(
            $this->equipment,
            $this->user,
            '2026-04-17',
            '2026-05-15'
        );

        $this->assertEquals('2026-04-17', $result->loan_date->format('Y-m-d'));
        $this->assertEquals('2026-05-15', $result->loan_expire_date->format('Y-m-d'));
    }
}

