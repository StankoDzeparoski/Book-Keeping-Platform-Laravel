<?php

namespace Tests\Feature\Controllers;

use App\Enums\Status;
use App\Enums\Condition;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EquipmentControllerTest extends TestCase
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
    public function manager_can_view_equipment_list(): void
    {
        $equipment = Equipment::factory()->create();

        $response = $this->actingAs($this->manager)
            ->get(route('equipment.index'));

        $response->assertStatus(200);
        $response->assertSee($equipment->brand);
    }

    /** @test */
    public function employee_can_view_equipment_list(): void
    {
        $equipment = Equipment::factory()->create([
            'status' => Status::AVAILABLE,
        ]);

        $response = $this->actingAs($this->employee)
            ->get(route('equipment.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function employee_cannot_see_broken_equipment(): void
    {
        $brokenEquipment = Equipment::factory()->create([
            'condition' => Condition::BROKEN,
        ]);

        $response = $this->actingAs($this->employee)
            ->get(route('equipment.index'));

        $response->assertStatus(200);
        $response->assertDontSee($brokenEquipment->brand);
    }

    /** @test */
    public function manager_can_see_broken_equipment(): void
    {
        $brokenEquipment = Equipment::factory()->create([
            'condition' => Condition::BROKEN,
        ]);

        $response = $this->actingAs($this->manager)
            ->get(route('equipment.index'));

        $response->assertStatus(200);
        $response->assertSee($brokenEquipment->brand);
    }

    /** @test */
    public function manager_can_create_equipment(): void
    {
        $response = $this->actingAs($this->manager)
            ->post(route('equipment.store'), [
                'brand' => 'Dell',
                'model' => 'XPS',
                'category' => 'Laptop',
                'cost' => 1200,
                'condition' => 'new',
                'acquisition_date' => '2026-04-15',
                'storage_location' => 'Room 101',
            ]);

        $response->assertRedirect(route('equipment.index'));
        $this->assertDatabaseHas('equipment', ['brand' => 'Dell']);
    }

    /** @test */
    public function employee_cannot_create_equipment(): void
    {
        $response = $this->actingAs($this->employee)
            ->post(route('equipment.store'), [
                'brand' => 'Dell',
                'model' => 'XPS',
                'category' => 'Laptop',
                'cost' => 1200,
                'condition' => 'new',
                'acquisition_date' => '2026-04-15',
                'storage_location' => 'Room 101',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function equipment_can_be_loaned(): void
    {
        $equipment = Equipment::factory()->create([
            'status' => Status::AVAILABLE,
        ]);

        $response = $this->actingAs($this->manager)
            ->post(route('equipment.loan', $equipment), [
                'user_id' => $this->employee->id,
                'loan_date' => '2026-04-17',
                'loan_expire_date' => '2026-05-01',
            ]);

        $response->assertRedirect(route('equipment.index'));

        $equipment->refresh();
        $this->assertEquals(Status::ASSIGNED, $equipment->status);
        $this->assertEquals($this->employee->id, $equipment->user_id);
    }

    /** @test */
    public function equipment_can_be_returned(): void
    {
        $equipment = Equipment::factory()->create([
            'status' => Status::ASSIGNED,
            'user_id' => $this->employee->id,
            'loan_date' => '2026-04-17',
            'loan_expire_date' => '2026-05-01',
        ]);

        $today = now()->format('Y-m-d');

        $response = $this->actingAs($this->manager)
            ->post(route('equipment.return', $equipment), [
                'return_date' => $today,
            ]);

        $response->assertRedirect(route('equipment.index'));

        $equipment->refresh();
        $this->assertEquals(Status::AVAILABLE, $equipment->status);
        $this->assertNull($equipment->user_id);
    }

    /** @test */
    public function equipment_can_be_repaired(): void
    {
        $equipment = Equipment::factory()->create([
            'condition' => Condition::BROKEN,
            'status' => Status::AVAILABLE,
        ]);

        $response = $this->actingAs($this->manager)
            ->post(route('equipment.repair', $equipment), [
                'description' => 'Screen replaced',
                'cost' => 150,
                'maintenance_date' => '2026-04-15',
            ]);

        $response->assertRedirect(route('equipment.show', $equipment));

        $equipment->refresh();
        $this->assertEquals(Status::REPAIR, $equipment->status);
    }

    /** @test */
    public function equipment_repair_can_be_finished(): void
    {
        $equipment = Equipment::factory()->create([
            'status' => Status::REPAIR,
            'condition' => Condition::BROKEN,
        ]);

        $response = $this->actingAs($this->manager)
            ->post(route('equipment.finishRepair', $equipment));

        $response->assertRedirect(route('equipment.show', $equipment));

        $equipment->refresh();
        $this->assertEquals(Status::AVAILABLE, $equipment->status);
        $this->assertEquals(Condition::USED, $equipment->condition);
    }

    /** @test */
    public function employee_cannot_repair_equipment(): void
    {
        $equipment = Equipment::factory()->create([
            'condition' => Condition::BROKEN,
        ]);

        $response = $this->actingAs($this->employee)
            ->post(route('equipment.repair', $equipment), [
                'description' => 'Screen replaced',
                'cost' => 150,
                'maintenance_date' => '2026-04-15',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function employee_cannot_finish_repair(): void
    {
        $equipment = Equipment::factory()->create([
            'status' => Status::REPAIR,
        ]);

        $response = $this->actingAs($this->employee)
            ->post(route('equipment.finishRepair', $equipment));

        $response->assertStatus(403);
    }

    /** @test */
    public function assigned_user_can_return_equipment(): void
    {
        $equipment = Equipment::factory()->create([
            'status' => Status::ASSIGNED,
            'user_id' => $this->employee->id,
            'loan_date' => '2026-04-17',
            'loan_expire_date' => '2026-05-01',
        ]);

        $today = now()->format('Y-m-d');

        $response = $this->actingAs($this->employee)
            ->post(route('equipment.return', $equipment), [
                'return_date' => $today,
            ]);

        $response->assertRedirect(route('equipment.index'));
    }

    /** @test */
    public function other_user_cannot_return_equipment(): void
    {
        $otherEmployee = User::factory()->create(['role' => 'Employee']);
        $equipment = Equipment::factory()->create([
            'status' => Status::ASSIGNED,
            'user_id' => $this->employee->id,
        ]);

        $response = $this->actingAs($otherEmployee)
            ->post(route('equipment.return', $equipment), [
                'return_date' => now()->format('Y-m-d'),
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function loan_date_must_be_after_or_equal_to_today(): void
    {
        $equipment = Equipment::factory()->create([
            'status' => Status::AVAILABLE,
        ]);

        $yesterday = now()->subDay()->format('Y-m-d');

        $response = $this->actingAs($this->manager)
            ->post(route('equipment.loan', $equipment), [
                'user_id' => $this->employee->id,
                'loan_date' => $yesterday,
                'loan_expire_date' => now()->format('Y-m-d'),
            ]);

        $response->assertSessionHasErrors('loan_date');
    }

    /** @test */
    public function return_date_cannot_exceed_loan_expiration(): void
    {
        $equipment = Equipment::factory()->create([
            'status' => Status::ASSIGNED,
            'user_id' => $this->employee->id,
            'loan_date' => '2026-04-17',
            'loan_expire_date' => '2026-04-30',
        ]);

        $response = $this->actingAs($this->manager)
            ->post(route('equipment.return', $equipment), [
                'return_date' => '2026-05-01',
            ]);

        $response->assertSessionHasErrors('return_date');
    }
}

