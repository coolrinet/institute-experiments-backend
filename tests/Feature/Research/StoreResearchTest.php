<?php

namespace Tests\Feature\Research;

use App\Models\Machinery;
use App\Models\MachineryParameter;
use App\Models\User;
use Database\Seeders\MachineryParameterSeeder;
use Database\Seeders\MachinerySeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreResearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            UserSeeder::class,
            MachinerySeeder::class,
            MachineryParameterSeeder::class,
        ]);

        $this->user = User::first();
        $this->machinery = Machinery::inRandomOrder()->first();
        $this->parameters = MachineryParameter::whereMachineryId($this->machinery->id)
            ->orWhereNull('machinery_id')->pluck('id')->all();
        $this->participants = User::whereNot('id', $this->user->id)
            ->inRandomOrder()->take(3)->pluck('id')->all();
    }

    public function test_authenticated_user_can_store_a_new_public_research(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('research.store'), [
                'name' => 'Test research',
                'description' => 'Test description',
                'is_public' => true,
                'parameters' => $this->parameters,
                'machinery_id' => $this->machinery->id,
            ]);

        $response->assertCreated();
        $this->assertDatabaseCount('research', 1);
        $this->assertDatabaseCount('research_machinery_parameter', count($this->parameters));
    }

    public function test_authenticated_user_can_store_a_new_private_research(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('research.store'), [
                'name' => 'Test research',
                'description' => 'Test description',
                'is_public' => false,
                'participants' => $this->participants,
                'parameters' => $this->parameters,
                'machinery_id' => $this->machinery->id,
            ]);

        $response->assertCreated();
        $this->assertDatabaseCount('research', 1);
        $this->assertDatabaseCount('research_machinery_parameter', count($this->parameters));
        $this->assertDatabaseCount('research_user', count($this->participants));
    }

    public function test_unauthenticated_user_cannot_store_a_new_research(): void
    {
        $response = $this->postJson(route('research.store'), [
            'name' => 'Test research',
            'description' => 'Test description',
            'is_public' => true,
            'parameters' => $this->parameters,
            'machinery_id' => $this->machinery->id,
        ]);

        $response->assertUnauthorized();
        $this->assertDatabaseCount('research', 0);
        $this->assertDatabaseCount('research_machinery_parameter', 0);
    }

    public function test_user_cannot_store_a_research_without_name(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('research.store'), [
                'description' => 'Test description',
                'is_public' => true,
                'parameters' => $this->parameters,
                'machinery_id' => $this->machinery->id,
            ]);

        $response->assertUnprocessable();
        $response->assertInvalid('name');
    }

    public function test_user_cannot_store_a_research_without_parameters(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('research.store'), [
                'name' => 'Test research',
                'description' => 'Test description',
                'is_public' => true,
                'machinery_id' => $this->machinery->id,
            ]);

        $response->assertUnprocessable();
        $response->assertInvalid('parameters');
    }

    public function test_user_cannot_store_a_research_without_machinery(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('research.store'), [
                'name' => 'Test research',
                'description' => 'Test description',
                'is_public' => true,
                'parameters' => $this->parameters,
            ]);

        $response->assertUnprocessable();
        $response->assertInvalid('machinery_id');
    }
}
