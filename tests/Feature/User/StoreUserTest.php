<?php

namespace Tests\Feature\User;

use App\Models\User;
use Tests\TestCase;

class StoreUserTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->userData = [
            'first_name' => 'Name',
            'last_name' => 'Last name',
            'email' => 'user@test.org',
        ];
    }

    public function test_admin_can_create_user(): void
    {
        $response = $this->actingAs($this->admin)
            ->postJson(route('users.store'), $this->userData);

        $response->assertCreated();
        $this->assertDatabaseHas('users', $this->userData);
    }

    public function test_user_cannot_create_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('users.store'), $this->userData);

        $response->assertNotFound();
        $this->assertDatabaseMissing('users', $this->userData);
    }

    public function test_guest_cannot_create_user(): void
    {
        $response = $this->postJson(route('users.store'), $this->userData);

        $response->assertUnauthorized();
        $this->assertDatabaseMissing('users', $this->userData);
    }

    public function test_admin_cant_create_user_with_invalid_data(): void
    {
        $response = $this->actingAs($this->admin)
            ->postJson(route('users.store'));

        $response->assertUnprocessable();
        $response->assertInvalid(['email', 'first_name', 'last_name']);
    }
}
