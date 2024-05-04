<?php

namespace Tests\Feature\Machinery;

use App\Models\Machinery;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateMachineryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->machinery = Machinery::factory()
            ->recycle($this->user)
            ->create([
                'name' => 'Test machinery',
                'description' => 'Test machinery description',
            ]);
    }

    public function test_authenticated_user_can_update_machinery(): void
    {
        $response = $this->actingAs($this->user)
            ->putJson(route('machineries.update', $this->machinery), [
                'name' => 'Updated machinery',
                'description' => 'Updated machinery description',
            ]);

        $response->assertNoContent();

        $this->assertDatabaseMissing('machineries', [
            'name' => 'Test machinery',
            'description' => 'Test machinery description',
        ]);
        $this->assertDatabaseHas('machineries', [
            'name' => 'Updated machinery',
            'description' => 'Updated machinery description',
        ]);
    }

    public function test_user_cannot_update__another_user_machinery(): void
    {
        $anotherUser = User::factory()->create();

        $response = $this->actingAs($anotherUser)
            ->putJson(route('machineries.update', $this->machinery), [
                'name' => 'Updated machinery',
                'description' => 'Updated machinery description',
            ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('machineries', [
            'name' => 'Test machinery',
            'description' => 'Test machinery description',
        ]);
    }

    public function test_user_cannot_update_machinery_with_invalid_data(): void
    {
        $response = $this->actingAs($this->user)
            ->putJson(route('machineries.update', $this->machinery), [
                'name' => '',
                'description' => '',
            ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrorFor('name');
        $this->assertDatabaseHas('machineries', [
            'name' => 'Test machinery',
            'description' => 'Test machinery description',
        ]);
    }

    public function test_unauthenticated_user_cannot_update_machinery(): void
    {
        $response = $this->putJson(route('machineries.update', $this->machinery), [
            'name' => 'Updated machinery',
            'description' => 'Updated machinery description',
        ]);

        $response->assertUnauthorized();
        $this->assertDatabaseHas('machineries', [
            'name' => 'Test machinery',
            'description' => 'Test machinery description',
        ]);
    }

    public function test_user_cannot_update_nonexistent_machinery(): void
    {
        $id = Machinery::max('id') + 1;

        $response = $this->actingAs($this->user)
            ->putJson(route('machineries.update', $id), [
                'name' => 'Updated machinery',
                'description' => 'Updated machinery description',
            ]);

        $response->assertNotFound();
        $this->assertDatabaseHas('machineries', [
            'name' => 'Test machinery',
            'description' => 'Test machinery description',
        ]);
    }
}
