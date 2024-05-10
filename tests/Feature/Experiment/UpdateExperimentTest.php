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

class UpdateExperimentTest extends TestCase
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

        $this->quantitativeInputs =
            $this->prepareQuantitativeValues($research, MachineryParameterTypeEnum::INPUT);
        $this->qualityInputs =
            $this->prepareQualityValues($research, MachineryParameterTypeEnum::INPUT);
        $this->quantitativeOutputs =
            $this->prepareQuantitativeValues($research, MachineryParameterTypeEnum::OUTPUT);
        $this->qualityOutputs =
            $this->prepareQualityValues($research, MachineryParameterTypeEnum::OUTPUT);

        $this->experiment->quantitativeInputs()->attach($this->quantitativeInputs);
        $this->experiment->qualityInputs()->attach($this->qualityInputs);
        $this->experiment->quantitativeOutputs()->attach($this->quantitativeOutputs);
        $this->experiment->qualityOutputs()->attach($this->qualityOutputs);

        $this->data = [
            'name' => 'Test Experiment Updated',
            'date' => $this->faker->date(),
            'quantitative_inputs' => $this->quantitativeInputs,
            'quality_inputs' => $this->qualityInputs,
            'quantitative_outputs' => $this->quantitativeOutputs,
            'quality_outputs' => $this->qualityOutputs,
        ];
    }

    public function test_user_can_update_experiment(): void
    {

        $response = $this->actingAs($this->experiment->user)
            ->putJson(route('research.experiments.update', [
                'research' => $this->experiment->research,
                'experiment' => $this->experiment,
            ]), $this->data);

        $response->assertNoContent();
        $this->assertDatabaseHas('experiments', [
            'name' => $this->data['name'],
        ]);
    }

    public function test_participant_of_research_cannot_update_other_users_experiment(): void
    {
        $participant = $this->experiment->research->participants()->first();

        $response = $this->actingAs($participant)
            ->putJson(route('research.experiments.update', [
                'research' => $this->experiment->research,
                'experiment' => $this->experiment,
            ]), $this->data);

        $response->assertForbidden();
    }

    public function test_unauthorized_user_cannot_update_experiment(): void
    {
        $unauthorizedUser = User::factory()->create();

        $response = $this->actingAs($unauthorizedUser)
            ->putJson(route('research.experiments.update', [
                'research' => $this->experiment->research,
                'experiment' => $this->experiment,
            ]), $this->data);

        $response->assertForbidden();
    }

    public function test_unauthenticated_user_cannot_update_experiment(): void
    {
        $response = $this->putJson(route('research.experiments.update', [
            'research' => $this->experiment->research,
            'experiment' => $this->experiment,
        ]), $this->data);

        $response->assertUnauthorized();
    }

    public function test_user_cannot_update_experiment_with_invalid_data(): void
    {
        $response = $this->actingAs($this->experiment->user)
            ->putJson(route('research.experiments.update', [
                'research' => $this->experiment->research,
                'experiment' => $this->experiment,
            ]));

        $response->assertInvalid(['name',
            'date',
            'quantitative_inputs',
            'quality_inputs',
            'quantitative_outputs',
            'quality_outputs',
        ]);
    }

    public function test_user_cannot_update_experiment_that_does_not_exist(): void
    {
        $experimentId = Experiment::max('id') + 1;

        $response = $this->actingAs($this->experiment->user)
            ->putJson(route('research.experiments.update', [
                'research' => $this->experiment->research,
                'experiment' => $experimentId,
            ]), $this->data);

        $response->assertNotFound();
    }

    public function test_user_cannot_update_experiment_when_research_does_not_exist(): void
    {
        $researchId = Research::max('id') + 1;

        $response = $this->actingAs($this->experiment->user)
            ->putJson(route('research.experiments.update', [
                'research' => $researchId,
                'experiment' => $this->experiment->id,
            ]), $this->data);

        $response->assertNotFound();
    }
}
