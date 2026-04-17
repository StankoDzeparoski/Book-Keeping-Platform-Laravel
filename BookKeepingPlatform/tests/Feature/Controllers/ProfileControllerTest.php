<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'role' => 'Employee',
        ]);
    }

    /** @test */
    public function authenticated_user_can_view_profile_edit_page(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertSee($this->user->name);
        $response->assertSee($this->user->email);
    }

    /** @test */
    public function unauthenticated_user_cannot_view_profile(): void
    {
        $response = $this->get(route('profile.edit'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function user_can_update_profile_information(): void
    {
        $response = $this->actingAs($this->user)
            ->patch(route('profile.update'), [
                'name' => 'Updated Name',
                'surname' => 'UpdatedSurname',
                'email' => 'updated@example.com',
            ]);

        $response->assertRedirect(route('profile.edit'));

        $this->user->refresh();
        $this->assertEquals('Updated Name', $this->user->name);
        $this->assertEquals('updated@example.com', $this->user->email);
    }

    /** @test */
    public function user_cannot_update_profile_without_authentication(): void
    {
        $response = $this->patch(route('profile.update'), [
            'name' => 'New Name',
            'surname' => 'NewSurname',
            'email' => 'new@example.com',
        ]);

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function user_email_must_be_unique_when_updating(): void
    {
        $otherUser = User::factory()->create(['email' => 'other@example.com']);

        $response = $this->actingAs($this->user)
            ->patch(route('profile.update'), [
                'name' => 'Updated Name',
                'surname' => 'UpdatedSurname',
                'email' => 'other@example.com',
            ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function user_can_update_email_to_same_email(): void
    {
        $response = $this->actingAs($this->user)
            ->patch(route('profile.update'), [
                'name' => $this->user->name,
                'surname' => $this->user->surname,
                'email' => $this->user->email,
            ]);

        $response->assertRedirect(route('profile.edit'));
    }

    /** @test */
    public function user_can_delete_account(): void
    {
        $userId = $this->user->id;

        $response = $this->actingAs($this->user)
            ->delete(route('profile.destroy'), [
                'password' => 'password',
            ]);

        $response->assertRedirect('/');
        $this->assertModelMissing($this->user);
    }

    /** @test */
    public function user_cannot_delete_account_without_password_confirmation(): void
    {
        $response = $this->actingAs($this->user)
            ->delete(route('profile.destroy'), [
                'password' => 'wrong-password',
            ]);

        $response->assertSessionHasErrorsIn('userDeletion', 'password');
        $this->assertModelExists($this->user);
    }

    /** @test */
    public function unauthenticated_user_cannot_delete_profile(): void
    {
        $response = $this->delete(route('profile.destroy'), [
            'password' => 'password',
        ]);

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function profile_page_loads_with_correct_user_data(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('profile.edit'));

        $response->assertStatus(200);
        $this->assertTrue(
            str_contains($response->getContent(), $this->user->email)
        );
    }

    /** @test */
    public function user_can_update_only_name(): void
    {
        $response = $this->actingAs($this->user)
            ->patch(route('profile.update'), [
                'name' => 'New Name',
                'surname' => $this->user->surname,
                'email' => $this->user->email,
            ]);

        $response->assertRedirect(route('profile.edit'));

        $this->user->refresh();
        $this->assertEquals('New Name', $this->user->name);
    }

//    /** @test */
//    public function user_can_update_only_surname(): void
//    {
//        $response = $this->actingAs($this->user)
//            ->patch(route('profile.update'), [
//                'name' => $this->user->name,
//                'surname' => 'NewSurname',
//                'email' => $this->user->email,
//            ]);
//
//        $response->assertRedirect(route('profile.edit'));
//
//        $this->user->refresh();
//        $this->assertEquals('NewSurname', $this->user->surname);
//    }

    /** @test */
    public function profile_update_requires_valid_email(): void
    {
        $response = $this->actingAs($this->user)
            ->patch(route('profile.update'), [
                'name' => 'New Name',
                'surname' => 'NewSurname',
                'email' => 'invalid-email',
            ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function profile_update_requires_name(): void
    {
        $response = $this->actingAs($this->user)
            ->patch(route('profile.update'), [
                'name' => '',
                'surname' => 'NewSurname',
                'email' => 'new@example.com',
            ]);

        $response->assertSessionHasErrors('name');
    }

//    /** @test */
//    public function profile_update_requires_surname(): void
//    {
//        $response = $this->actingAs($this->user)
//            ->patch(route('profile.update'), [
//                'name' => 'NewName',
//                'surname' => '',
//                'email' => 'new@example.com',
//            ]);
//
//        $response->assertSessionHasErrors('surname');
//    }
}

