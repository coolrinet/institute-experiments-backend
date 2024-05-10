<?php

namespace Tests\Feature\Experiment;

use App\Models\Research;
use App\Models\User;
use Tests\TestCase;

class ExperimentListTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
        $this->research = Research::has('participants')->first();
        $this->user = $this->research->author;
    }

    public function test_author_can_see_research_experiments(): void
    {
        $experiments = $this->research->experiments;

        $response = $this->actingAs($this->user)
            ->getJson(route('research.experiments.index', ['research' => $this->research]));

        $response->assertOk();
        $response->assertJsonCount(min($experiments->count(), 15), 'data');
        $response->assertJsonPath('meta.total', $experiments->count());
    }

    public function test_participant_can_see_research_experiments(): void
    {
        $participant = $this->research->participants()->first();
        $experiments = $this->research->experiments;

        $response = $this->actingAs($participant)
            ->getJson(route('research.experiments.index', ['research' => $this->research]));

        $response->assertOk();
        $response->assertJsonCount(min($experiments->count(), 15), 'data');
        $response->assertJsonPath('meta.total', $experiments->count());
    }

    public function test_unauthenticated_user_cannot_see_research_experiments(): void
    {
        $response = $this->getJson(route('research.experiments.index', ['research' => $this->research]));

        $response->assertUnauthorized();
    }

    public function test_unauthorized_user_cannot_see_research_experiments(): void
    {
        $unauthorizedUser = User::factory()->create();

        $response = $this->actingAs($unauthorizedUser)
            ->getJson(route('research.experiments.index', ['research' => $this->research]));

        $response->assertForbidden();
    }

    public function test_user_cannot_see_experiments_of_research_that_not_found(): void
    {
        $id = Research::max('id') + 1;

        $response = $this->actingAs($this->user)
            ->getJson(route('research.experiments.index', ['research' => $id]));

        $response->assertNotFound();
    }
}
