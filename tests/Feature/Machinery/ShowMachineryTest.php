<?php

namespace Tests\Feature\Machinery;

use App\Models\Machinery;
use App\Models\User;
use Tests\TestCase;

class ShowMachineryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->machinery = Machinery::factory()
            ->recycle($this->user)
            ->create();
    }

    public function test_authenticated_user_can_view_machinery(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson(route('machineries.show', $this->machinery));

        $response->assertOk();
        $response->assertJsonStructure(['data' => ['id', 'name', 'description']]);
    }

    public function test_unauthenticated_user_cannot_view_machinery(): void
    {
        $response = $this->getJson(route('machineries.show', $this->machinery));

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_cannot_view_non_existing_machinery(): void
    {
        $id = Machinery::max('id') + 1;

        $response = $this->actingAs($this->user)
            ->getJson(route('machineries.show', $id));

        $response->assertNotFound();
    }
}
