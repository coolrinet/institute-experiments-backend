<?php

namespace Tests\Feature\Profile;

use App\Models\MachineryParameter;
use App\Models\User;
use Tests\TestCase;

class DeleteProfileTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_user_can_delete_profile(): void
    {
        $response = $this->actingAs($this->user)
            ->deleteJson(route('profile.delete'));

        $response->assertNoContent();

        $this->assertGuest('web');

        $this->assertDatabaseMissing('users', [
            'id' => $this->user->id,
        ]);
    }

    public function test_guest_cannot_delete_profile(): void
    {
        $response = $this->deleteJson(route('profile.delete'));

        $response->assertUnauthorized();
    }

    public function test_user_cannot_delete_profile_with_related_data(): void
    {
        MachineryParameter::factory()->for($this->user)->create();

        $response = $this->actingAs($this->user)
            ->deleteJson(route('profile.delete'));

        $response->assertConflict();

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
        ]);
    }
}
