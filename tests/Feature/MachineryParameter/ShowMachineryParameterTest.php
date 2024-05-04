<?php

namespace Tests\Feature\MachineryParameter;

use App\Models\MachineryParameter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowMachineryParameterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->machineryParameter = MachineryParameter::factory()->create([
            'name' => 'Test parameter',
        ]);
    }

    public function test_authenticated_user_can_see_machinery_parameter(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson(route('machinery-parameters.show', $this->machineryParameter));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => ['id', 'name', 'parameterType', 'valueType', 'machinery'],
        ]);
        $response->assertJsonPath('data.name', $this->machineryParameter->name);
    }

    public function test_unauthenticated_user_cannot_see_machinery_parameter(): void
    {
        $response = $this->getJson(route('machinery-parameters.show', $this->machineryParameter));

        $response->assertUnauthorized();
    }

    public function test_user_cannot_see_machinery_parameter_that_does_not_exist(): void
    {
        $id = MachineryParameter::max('id') + 1;

        $response = $this->actingAs($this->user)
            ->getJson(route('machinery-parameters.show', $id));

        $response->assertNotFound();
    }
}
