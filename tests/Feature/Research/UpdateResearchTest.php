<?php

namespace Tests\Feature\Research;

use App\Models\Machinery;
use App\Models\MachineryParameter;
use App\Models\User;
use Database\Seeders\MachineryParameterSeeder;
use Database\Seeders\MachinerySeeder;
use Database\Seeders\UserSeeder;
use Tests\TestCase;

class UpdateResearchTest extends TestCase
{
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
            ->inRandomOrder()->take(3)->get();

        $this->research = $this->user->research()->create([
            'name' => 'Test research',
            'description' => 'Test description',
            'is_public' => false,
            'participants' => $this->participants,
            'machinery_id' => $this->machinery->id,
            'parameters' => $this->parameters,
        ]);
    }

    public function test_author_can_update_a_research(): void
    {
        $response = $this->actingAs($this->user)
            ->putJson(route('research.update', $this->research), [
                'name' => 'Updated research',
                'description' => 'Updated description',
                'is_public' => false,
                'participants' => $this->participants->pluck('id')->all(),
                'machinery_id' => $this->machinery->id,
                'parameters' => $this->parameters,
            ]);

        $response->assertNoContent();
        $this->assertDatabaseHas('research', [
            'name' => 'Updated research',
            'description' => 'Updated description',
        ]);
    }

    public function test_author_can_update_a_machinery(): void
    {
        $newMachinery = Machinery::whereNot('id', $this->machinery->id)->first();
        $newParameters = MachineryParameter::whereMachineryId($newMachinery->id)
            ->orWhereNull('machinery_id')->pluck('id')->all();

        $response = $this->actingAs($this->user)
            ->putJson(route('research.update', $this->research), [
                'name' => 'Updated research',
                'description' => 'Updated description',
                'is_public' => false,
                'participants' => $this->participants->pluck('id')->all(),
                'machinery_id' => $newMachinery->id,
                'parameters' => $newParameters,
            ]);

        $response->assertNoContent();
        $this->assertDatabaseHas('research', [
            'name' => 'Updated research',
            'description' => 'Updated description',
        ]);
        $this->assertDatabaseCount('research_machinery_parameter', count($newParameters));
    }

    public function test_author_cannot_change_parameters_if_they_are_not_linked_to_machinery(): void
    {
        $newMachinery = Machinery::inRandomOrder()->whereNot('id', $this->machinery->id)
            ->has('parameters')->first();
        $newParameters = MachineryParameter::whereMachineryId($newMachinery->id)
            ->orWhereNull('machinery_id')->pluck('id')->all();

        $response = $this->actingAs($this->user)
            ->putJson(route('research.update', $this->research), [
                'name' => 'Updated research',
                'description' => 'Updated description',
                'is_public' => false,
                'participants' => $this->participants->pluck('id')->all(),
                'machinery_id' => $this->machinery->id,
                'parameters' => $newParameters,
            ]);

        $response->assertUnprocessable();
        $response->assertInvalid('parameters');
    }

    public function test_author_can_change_participants(): void
    {
        $response = $this->actingAs($this->user)
            ->putJson(route('research.update', $this->research), [
                'name' => 'Updated research',
                'description' => 'Updated description',
                'is_public' => false,
                'participants' => [],
                'machinery_id' => $this->machinery->id,
                'parameters' => $this->parameters,
            ]);

        $response->assertNoContent();
        $this->assertDatabaseHas('research', [
            'name' => 'Updated research',
            'description' => 'Updated description',
        ]);
        $this->assertDatabaseCount('research_user', 0);
    }

    public function test_participant_cannot_update_a_research(): void
    {
        $response = $this->actingAs($this->participants->first())
            ->putJson(route('research.update', $this->research), [
                'name' => 'Updated research',
                'description' => 'Updated description',
                'is_public' => false,
                'participants' => $this->participants->pluck('id')->all(),
                'machinery_id' => $this->machinery->id,
                'parameters' => $this->parameters,
            ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('research', [
            'name' => 'Updated research',
            'description' => 'Updated description',
        ]);
    }

    public function test_unauthenticated_user_cannot_update_a_research(): void
    {
        $response = $this->putJson(route('research.update', $this->research), [
            'name' => 'Updated research',
            'description' => 'Updated description',
            'is_public' => false,
            'participants' => $this->participants->pluck('id')->all(),
            'machinery_id' => $this->machinery->id,
            'parameters' => $this->parameters,
        ]);

        $response->assertUnauthorized();
        $this->assertDatabaseMissing('research', [
            'name' => 'Updated research',
            'description' => 'Updated description',
        ]);
    }
}
