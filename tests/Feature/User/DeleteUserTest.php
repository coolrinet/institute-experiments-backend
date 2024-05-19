<?php

namespace Tests\Feature\User;

use App\Models\User;
use Tests\TestCase;

class DeleteUserTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->user = User::factory()->create();
    }

    public function test_admin_can_delete_user(): void
    {
        $response = $this->actingAs($this->admin)
            ->deleteJson(route('users.destroy', $this->user));

        $response->assertNoContent();
        $this->assertModelMissing($this->user);
    }

    public function test_user_cannot_delete_other_user(): void
    {
        $response = $this->actingAs($this->user)
            ->deleteJson(route('users.destroy', $this->admin));

        $response->assertNotFound();
        $this->assertModelExists($this->admin);
    }
}
