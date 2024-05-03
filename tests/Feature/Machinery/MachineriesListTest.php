<?php

namespace Machinery;

use App\Models\Machinery;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MachineriesListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        Machinery::factory(45)->create();
    }

    public function test_authenticated_user_can_get_list_of_machineries(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson(
                route('machineries.index')
            );

        $response->assertOk();
        $response->assertJsonCount(15, 'data');
        $response->assertJsonPath('meta.last_page', 3);
    }

    public function test_authenticated_user_can_get_list_of_machineries_with_relations(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson(
                route('machineries.index', ['include' => 'user'])
            );

        $response->assertOk();
        $response->assertJsonCount(15, 'data');
        $this->assertNotTrue($response->assertJsonMissingPath('data.*.user'));
    }

    public function test_authenticated_user_cannot_get_list_of_machineries_with_invalid_relations(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson(
                route('machineries.index', ['include' => 'random-word'])
            );

        $response->assertNotFound();
    }

    public function test_unauthenticated_user_cannot_get_list_of_machineries(): void
    {
        $response = $this->getJson(
            route('machineries.index')
        );

        $response->assertUnauthorized();
    }
}
