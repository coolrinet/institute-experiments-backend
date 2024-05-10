<?php

namespace Tests\Feature\MachineryParameter;

use App\Models\MachineryParameter;
use App\Models\User;
use Tests\TestCase;

class DeleteMachineryParameterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->machineryParameter = MachineryParameter::factory()
            ->recycle($this->user)
            ->create();
    }

    public function test_authenticated_user_can_delete_machinery_parameter(): void
    {
        $response = $this->actingAs($this->user)
            ->deleteJson(route('machinery-parameters.destroy', $this->machineryParameter));

        $response->assertNoContent();
        $this->assertModelMissing($this->machineryParameter);
    }

    public function test_user_cannot_delete_other_user_machinery_parameter(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->deleteJson(route('machinery-parameters.destroy', $this->machineryParameter));

        $response->assertForbidden();
        $this->assertModelExists($this->machineryParameter);
    }

    public function test_user_cannot_delete_machinery_parameter_that_does_not_exist(): void
    {
        $id = MachineryParameter::max('id') + 1;

        $response = $this->actingAs($this->user)
            ->deleteJson(route('machinery-parameters.destroy', $id));

        $response->assertNotFound();
    }

    public function test_unauthenticated_user_cannot_delete_machinery_parameter(): void
    {
        $response = $this->deleteJson(route('machinery-parameters.destroy', $this->machineryParameter));

        $response->assertUnauthorized();
        $this->assertModelExists($this->machineryParameter);
    }
}
