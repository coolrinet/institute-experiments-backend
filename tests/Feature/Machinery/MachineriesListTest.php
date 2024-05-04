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

    public function test_user_can_get_list_of_machineries_by_name(): void
    {
        Machinery::factory()->create(['name' => 'Machine']);

        $response = $this->actingAs($this->user)
            ->getJson(
                route('machineries.index', ['name' => 'Machine'])
            );

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('meta.last_page', 1);
    }

    public function test_user_can_get_list_of_machineries_by_user_id(): void
    {
        $machineriesCount = Machinery::where('user_id', $this->user->id)
            ->count();

        $response = $this->actingAs($this->user)
            ->getJson(
                route('machineries.index', ['user_id' => $this->user->id])
            );

        $response->assertOk();
        $response->assertJsonCount(min($machineriesCount, 15), 'data');
        $response->assertJsonPath('meta.total', $machineriesCount);
    }

    public function test_unauthenticated_user_cannot_get_list_of_machineries(): void
    {
        $response = $this->getJson(
            route('machineries.index')
        );

        $response->assertUnauthorized();
    }
}
