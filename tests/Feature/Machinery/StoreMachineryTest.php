<?php

namespace Machinery;

use App\Models\User;
use Tests\TestCase;

class StoreMachineryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_authenticated_user_can_store_machinery(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('machineries.store'), [
                'name' => 'Test machinery',
                'description' => 'Test description',
            ]);

        $response->assertCreated();
        $this->assertDatabaseCount('machineries', 1);
    }

    public function test_authenticated_user_cannot_store_machinery_without_name(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('machineries.store'), [
                'description' => 'Test description',
            ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrorFor('name');
        $this->assertDatabaseCount('machineries', 0);
    }

    public function test_authenticated_user_cannot_store_machinery_with_long_name(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('machineries.store'), [
                'name' => str_repeat('a', 256),
                'description' => 'Test description',
            ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrorFor('name');
        $this->assertDatabaseCount('machineries', 0);
    }

    public function test_unauthenticated_user_cannot_store_machinery(): void
    {
        $response = $this->postJson(route('machineries.store'), [
            'name' => 'Test machinery',
            'description' => 'Test description',
        ]);

        $response->assertUnauthorized();
        $this->assertDatabaseCount('machineries', 0);
    }
}
