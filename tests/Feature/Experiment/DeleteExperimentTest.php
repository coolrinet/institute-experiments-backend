<?php

namespace Tests\Feature\Experiment;

use App\Enums\MachineryParameterTypeEnum;
use App\Models\Experiment;
use App\Models\Research;
use App\Models\User;
use Database\Seeders\MachineryParameterSeeder;
use Database\Seeders\MachinerySeeder;
use Database\Seeders\ResearchSeeder;
use Database\Seeders\UserSeeder;
use Tests\TestCase;

class DeleteExperimentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            UserSeeder::class,
            MachinerySeeder::class,
            MachineryParameterSeeder::class,
            ResearchSeeder::class,
        ]);

        $research = Research::has('participants')->first();
        $user = $research->author;
        $this->experiment = Experiment::factory()
            ->for($research)
            ->for($user)
            ->create([
                'name' => 'Test Experiment',
            ]);

        $quantitativeInputs =
            $this->prepareQuantitativeValues($research, MachineryParameterTypeEnum::INPUT);
        $qualityInputs =
            $this->prepareQualityValues($research, MachineryParameterTypeEnum::INPUT);
        $quantitativeOutputs =
            $this->prepareQuantitativeValues($research, MachineryParameterTypeEnum::OUTPUT);
        $qualityOutputs =
            $this->prepareQualityValues($research, MachineryParameterTypeEnum::OUTPUT);

        $this->experiment->quantitativeInputs()->attach($quantitativeInputs);
        $this->experiment->qualityInputs()->attach($qualityInputs);
        $this->experiment->quantitativeOutputs()->attach($quantitativeOutputs);
        $this->experiment->qualityOutputs()->attach($qualityOutputs);
    }

    public function test_user_can_delete_experiment(): void
    {
        $response = $this->actingAs($this->experiment->user)
            ->deleteJson(route('research.experiments.destroy', [
                'research' => $this->experiment->research,
                'experiment' => $this->experiment,
            ]));

        $response->assertNoContent();
        $this->assertModelMissing($this->experiment);
    }

    public function test_participant_of_research_cannot_delete_other_users_experiment(): void
    {
        $participant = $this->experiment->research->participants()->first();

        $response = $this->actingAs($participant)
            ->deleteJson(route('research.experiments.destroy', [
                'research' => $this->experiment->research,
                'experiment' => $this->experiment,
            ]));

        $response->assertForbidden();
        $this->assertModelExists($this->experiment);
    }

    public function test_unauthorized_user_cannot_delete_experiment(): void
    {
        $unauthorizedUser = User::factory()->create();

        $response = $this->actingAs($unauthorizedUser)
            ->deleteJson(route('research.experiments.destroy', [
                'research' => $this->experiment->research,
                'experiment' => $this->experiment,
            ]));

        $response->assertForbidden();
        $this->assertModelExists($this->experiment);
    }

    public function test_unauthenticated_user_cannot_delete_experiment(): void
    {
        $response = $this->deleteJson(route('research.experiments.destroy', [
            'research' => $this->experiment->research,
            'experiment' => $this->experiment,
        ]));

        $response->assertUnauthorized();
        $this->assertModelExists($this->experiment);
    }

    public function test_user_cannot_delete_experiment_that_does_not_exist(): void
    {
        $experimentId = Experiment::max('id') + 1;

        $response = $this->actingAs($this->experiment->user)
            ->deleteJson(route('research.experiments.destroy', [
                'research' => $this->experiment->research,
                'experiment' => $experimentId,
            ]));

        $response->assertNotFound();
    }

    public function test_user_cannot_delete_experiment_when_research_does_not_exist(): void
    {
        $researchId = Research::max('id') + 1;

        $response = $this->actingAs($this->experiment->user)
            ->deleteJson(route('research.experiments.destroy', [
                'research' => $researchId,
                'experiment' => $this->experiment->id,
            ]));

        $response->assertNotFound();
    }
}
