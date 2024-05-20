<?php

namespace Tests\Feature\Profile;

use App\Models\User;
use Tests\TestCase;

class UpdateProfileTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->newData = [
            'first_name' => 'Test',
            'last_name' => 'Test',
            'middle_name' => 'Test',
            'email' => 'test@test.org',
        ];
    }

    public function test_user_can_update_profile(): void
    {
        $input = $this->newData + [
            'current_password' => 'password',
            'current_password_confirmation' => 'password',
        ];

        $response = $this->actingAs($this->user)
            ->putJson(route('profile.update'), $input);

        $response->assertNoContent();
        $this->assertDatabaseHas('users', $this->newData);
    }

    public function test_user_cannot_update_profile_with_invalid_password(): void
    {
        $input = $this->newData + [
            'current_password' => 'wrong-password',
            'current_password_confirmation' => 'wrong-password',
        ];

        $response = $this->actingAs($this->user)
            ->putJson(route('profile.update'), $input);

        $response->assertInvalid('current_password');
        $this->assertDatabaseMissing('users', $this->newData);
    }

    public function test_user_cannot_update_profile_with_invalid_password_confirmation(): void
    {
        $input = $this->newData + [
            'current_password' => 'password',
            'current_password_confirmation' => 'wrong-password',
        ];

        $response = $this->actingAs($this->user)
            ->putJson(route('profile.update'), $input);

        $response->assertInvalid('current_password');
        $this->assertDatabaseMissing('users', $this->newData);
    }

    public function test_user_cannot_update_profile_with_invalid_email(): void
    {
        User::factory()->create([
            'email' => $this->newData['email'],
        ]);

        $input = $this->newData + [
            'current_password' => 'password',
            'current_password_confirmation' => 'password',
        ];

        $response = $this->actingAs($this->user)
            ->putJson(route('profile.update'), $input);

        $response->assertInvalid('email');
        $this->assertDatabaseMissing('users', $this->newData);
    }

    public function test_user_cannot_update_profile_without_email(): void
    {
        $input = $this->newData + [
            'current_password' => 'password',
            'current_password_confirmation' => 'password',
        ];

        $input['email'] = null;

        $response = $this->actingAs($this->user)
            ->putJson(route('profile.update'), $input);

        $response->assertInvalid(['email']);
        $this->assertDatabaseMissing('users', $this->newData);
    }
}
