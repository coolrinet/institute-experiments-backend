<?php

namespace Tests\Feature\Machinery;

use App\Models\Machinery;
use App\Models\MachineryParameter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteMachineryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->machinery = $this->user->machineries()->create([
            'name' => 'test machinery',
            'description' => 'test machinery description',
        ]);
    }

    public function test_authenticated_user_can_delete_machinery(): void
    {
        $response = $this->actingAs($this->user)
            ->deleteJson(route('machineries.destroy', $this->machinery));

        $response->assertNoContent();
        $this->assertModelMissing($this->machinery);
    }

    public function test_unauthenticated_user_cannot_delete_machinery(): void
    {
        $response = $this->deleteJson(route('machineries.destroy', $this->machinery));

        $response->assertUnauthorized();
        $this->assertModelExists($this->machinery);
    }

    public function test_user_cannot_delete_another_user_machinery(): void
    {
        $anotherUser = User::factory()->create();

        $response = $this->actingAs($anotherUser)
            ->deleteJson(route('machineries.destroy', $this->machinery));

        $response->assertForbidden();
        $this->assertModelExists($this->machinery);
    }

    public function test_user_cannot_delete_machinery_that_has_parameters(): void
    {
        MachineryParameter::factory()->create([
            'machinery_id' => $this->machinery->id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson(route('machineries.destroy', $this->machinery));

        $response->assertConflict();
        $this->assertModelExists($this->machinery);
    }

    public function test_user_cannot_delete_machinery_that_does_not_exist(): void
    {
        $id = Machinery::max('id') + 1;

        $response = $this->actingAs($this->user)
            ->deleteJson(route('machineries.destroy', $id));

        $response->assertNotFound();
    }
}
