<?php

namespace Tests\Feature\Experiment;

use App\Models\Experiment;
use App\Models\Research;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowExperimentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
        $this->research = Research::has('participants')->first();
        $this->user = $this->research->author;
        $this->experiment = $this->research->experiments()->first();
    }

    public function test_author_of_research_can_see_experiment(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson(route('research.experiments.show', [
                'research' => $this->research,
                'experiment' => $this->experiment,
            ]));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'date',
                'research',
                'user',
                'quantitativeInputs',
                'qualityInputs',
                'quantitativeOutputs',
                'qualityOutputs',
            ],
        ]);
        $response->assertJsonPath('data.id', $this->experiment->id);
    }

    public function test_participant_of_research_can_see_experiment(): void
    {
        $participant = $this->research->participants()->first();
        $response = $this->actingAs($participant)
            ->getJson(route('research.experiments.show', [
                'research' => $this->research,
                'experiment' => $this->experiment,
            ]));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'date',
                'research',
                'user',
                'quantitativeInputs',
                'qualityInputs',
                'quantitativeOutputs',
                'qualityOutputs',
            ],
        ]);
        $response->assertJsonPath('data.id', $this->experiment->id);
    }

    public function test_unauthenticated_user_cannot_see_experiment(): void
    {
        $response = $this->getJson(route('research.experiments.show', [
            'research' => $this->research,
            'experiment' => $this->experiment,
        ]));

        $response->assertUnauthorized();
    }

    public function test_unauthorized_user_cannot_see_experiment(): void
    {
        $unauthorizedUser = User::factory()->create();

        $response = $this->actingAs($unauthorizedUser)
            ->getJson(route('research.experiments.show', [
                'research' => $this->research,
                'experiment' => $this->experiment,
            ]));

        $response->assertForbidden();
    }

    public function test_user_cannot_see_experiment_that_does_not_exist(): void
    {
        $id = Experiment::max('id') + 1;

        $response = $this->actingAs($this->user)
            ->getJson(route('research.experiments.show', [
                'research' => $this->research,
                'experiment' => $id,
            ]));

        $response->assertNotFound();
    }

    public function test_user_cannot_see_experiment_of_non_existing_research(): void
    {
        $id = Research::max('id') + 1;

        $response = $this->actingAs($this->user)
            ->getJson(route('research.experiments.show', [
                'research' => $id,
                'experiment' => $this->experiment,
            ]));

        $response->assertNotFound();
    }
}
