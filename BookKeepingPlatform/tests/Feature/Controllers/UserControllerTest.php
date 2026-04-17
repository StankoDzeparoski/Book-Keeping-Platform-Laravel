<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
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
    public function manager_can_view_users_list(): void
    {
        $response = $this->actingAs($this->manager)
            ->get(route('users.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function employee_cannot_view_users_list(): void
    {
        $response = $this->actingAs($this->employee)
            ->get(route('users.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function manager_can_view_create_user_form(): void
    {
        $response = $this->actingAs($this->manager)
            ->get(route('users.create'));

        $response->assertStatus(200);
    }

    /** @test */
    public function employee_cannot_view_create_user_form(): void
    {
        $response = $this->actingAs($this->employee)
            ->get(route('users.create'));

        $response->assertStatus(403);
    }

    /** @test */
    public function manager_can_create_user(): void
    {
        $response = $this->actingAs($this->manager)
            ->post(route('users.store'), [
                'name' => 'John',
                'surname' => 'Doe',
                'dob' => '15/06/1990',
                'email' => 'john@example.com',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'role' => 'Employee',
            ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'John',
            'email' => 'john@example.com',
        ]);
    }

    /** @test */
    public function employee_cannot_create_user(): void
    {
        $response = $this->actingAs($this->employee)
            ->post(route('users.store'), [
                'name' => 'John',
                'surname' => 'Doe',
                'dob' => '15/06/1990',
                'email' => 'john@example.com',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'role' => 'Employee',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function manager_can_view_single_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->manager)
            ->get(route('users.show', $user));

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }

    /** @test */
    public function employee_cannot_view_other_users(): void
    {
        $otherEmployee = User::factory()->create();

        $response = $this->actingAs($this->employee)
            ->get(route('users.show', $otherEmployee));

        $response->assertStatus(403);
    }

    /** @test */
    public function manager_can_view_edit_form(): void
    {
        $user = User::factory()->create(['role' => 'Employee']);

        $response = $this->actingAs($this->manager)
            ->get(route('users.edit', $user));

        $response->assertStatus(200);
    }

    /** @test */
    public function employee_cannot_view_edit_form(): void
    {
        $otherEmployee = User::factory()->create();

        $response = $this->actingAs($this->employee)
            ->get(route('users.edit', $otherEmployee));

        $response->assertStatus(403);
    }

    /** @test */
    public function manager_can_update_user(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
        ]);

        $response = $this->actingAs($this->manager)
            ->patch(route('users.update', $user), [
                'name' => 'New Name',
                'surname' => 'NewSurname',
                'dob' => '15/06/1990',
                'email' => $user->email,
                'role' => 'Manager',
            ]);

        $response->assertRedirect(route('users.show', $user));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
        ]);
    }

    /** @test */
    public function employee_cannot_update_users(): void
    {
        $otherEmployee = User::factory()->create();

        $response = $this->actingAs($this->employee)
            ->patch(route('users.update', $otherEmployee), [
                'name' => 'Updated',
                'surname' => 'User',
                'dob' => '15/06/1990',
                'email' => 'new@example.com',
                'role' => 'Manager',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function manager_can_delete_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->manager)
            ->delete(route('users.destroy', $user));

        $response->assertRedirect(route('users.index'));
        $this->assertModelMissing($user);
    }

    /** @test */
    public function employee_cannot_delete_users(): void
    {
        $otherEmployee = User::factory()->create();

        $response = $this->actingAs($this->employee)
            ->delete(route('users.destroy', $otherEmployee));

        $response->assertStatus(403);
    }

    /** @test */
    public function user_has_equipment_relationship(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->equipment());
    }

    /** @test */
    public function users_list_shows_all_users(): void
    {
        $user1 = User::factory()->create(['name' => 'Alice']);
        $user2 = User::factory()->create(['name' => 'Bob']);

        $response = $this->actingAs($this->manager)
            ->get(route('users.index'));

        $response->assertStatus(200);
        $response->assertSee('Alice');
        $response->assertSee('Bob');
    }

    /** @test */
    public function user_email_must_be_unique(): void
    {
        $existingUser = User::factory()->create(['email' => 'test@example.com']);

        $response = $this->actingAs($this->manager)
            ->post(route('users.store'), [
                'name' => 'John',
                'surname' => 'Doe',
                'dob' => '15/06/1990',
                'email' => 'test@example.com',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'role' => 'Employee',
            ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function user_role_can_be_manager_or_employee(): void
    {
        $response = $this->actingAs($this->manager)
            ->post(route('users.store'), [
                'name' => 'Jane',
                'surname' => 'Doe',
                'dob' => '20/03/1985',
                'email' => 'jane@example.com',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'role' => 'Manager',
            ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com',
            'role' => 'Manager',
        ]);
    }
}

