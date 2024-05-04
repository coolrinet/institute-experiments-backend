<?php

namespace Tests\Feature\MachineryParameter;

use App\Models\MachineryParameter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MachineryParametersListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        MachineryParameter::factory(30)->create();
    }

    public function test_authenticated_user_can_get_list_of_machinery_parameters(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson(route('machinery-parameters.index'));

        $response->assertOk();
        $response->assertJsonCount(15, 'data');
        $response->assertJsonPath('meta.total', 30);
        $response->assertJsonPath('meta.last_page', 2);
    }

    public function test_unauthenticated_user_cannot_get_list_of_machinery_parameters(): void
    {
        $response = $this->getJson(route('machinery-parameters.index'));

        $response->assertUnauthorized();
    }
}
