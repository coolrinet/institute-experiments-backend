<?php

namespace Tests\Feature\Experiment;

use App\Enums\MachineryParameterTypeEnum;
use App\Enums\MachineryParameterValueTypeEnum;
use App\Models\MachineryParameter;
use App\Models\Research;
use App\Models\User;
use Database\Seeders\MachineryParameterSeeder;
use Database\Seeders\MachinerySeeder;
use Database\Seeders\ResearchSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StoreExperimentTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            UserSeeder::class,
            MachinerySeeder::class,
            MachineryParameterSeeder::class,
            ResearchSeeder::class,
        ]);

        $this->research = Research::has('participants')->first();
        $this->user = $this->research->author;

        $this->quantitativeInputs =
            $this->prepareQuantitativeValues($this->research, MachineryParameterTypeEnum::INPUT);
        $this->qualityInputs =
            $this->prepareQualityValues($this->research, MachineryParameterTypeEnum::INPUT);
        $this->quantitativeOutputs =
            $this->prepareQuantitativeValues($this->research, MachineryParameterTypeEnum::OUTPUT);
        $this->qualityOutputs =
            $this->prepareQualityValues($this->research, MachineryParameterTypeEnum::OUTPUT);

        $this->experimentData = [
            'name' => $this->faker->word(),
            'date' => $this->faker->date(),
            'quantitative_inputs' => $this->quantitativeInputs,
            'quality_inputs' => $this->qualityInputs,
            'quantitative_outputs' => $this->quantitativeOutputs,
            'quality_outputs' => $this->qualityOutputs,
        ];
    }

    protected function prepareQuantitativeValues(
        Research $research,
        MachineryParameterTypeEnum $parameterType
    ): array {
        return $research->parameters()
            ->whereParameterType($parameterType->value)
            ->whereValueType(MachineryParameterValueTypeEnum::QUANTITATIVE->value)
            ->get()->map(function (MachineryParameter $machineryParameter) {
                return [
                    'parameter_id' => $machineryParameter->id,
                    'value' => $this->faker->randomFloat(2, 0, 100),
                ];
            })->toArray();
    }

    protected function prepareQualityValues(
        Research $research,
        MachineryParameterTypeEnum $parameterType
    ): array {
        return $research->parameters()
            ->whereParameterType($parameterType->value)
            ->whereValueType(MachineryParameterValueTypeEnum::QUALITY->value)
            ->get()->map(function (MachineryParameter $machineryParameter) {
                return [
                    'parameter_id' => $machineryParameter->id,
                    'value' => $this->faker->unique()->word(),
                ];
            })->toArray();
    }

    public function test_author_of_research_can_store_experiment(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('research.experiments.store', [
                'research' => $this->research,
            ]), $this->experimentData);

        $response->assertCreated();
        $this->assertDatabaseCount('experiments', 1);
        $this->assertDatabaseHas('experiments', [
            'name' => $this->experimentData['name'],
            'user_id' => $this->user->id,
        ]);
        $this->assertDatabaseCount('experiment_quantitative_inputs', count($this->quantitativeInputs));
        $this->assertDatabaseCount('experiment_quality_inputs', count($this->qualityInputs));
        $this->assertDatabaseCount('experiment_quantitative_outputs', count($this->quantitativeOutputs));
        $this->assertDatabaseCount('experiment_quality_outputs', count($this->qualityOutputs));
    }

    public function test_participant_of_research_can_store_experiment(): void
    {
        $participant = $this->research->participants()->first();

        $response = $this->actingAs($participant)
            ->postJson(route('research.experiments.store', [
                'research' => $this->research,
            ]), $this->experimentData);

        $response->assertCreated();
        $this->assertDatabaseCount('experiments', 1);
        $this->assertDatabaseHas('experiments', [
            'name' => $this->experimentData['name'],
            'user_id' => $participant->id,
        ]);
        $this->assertDatabaseCount('experiment_quantitative_inputs', count($this->quantitativeInputs));
        $this->assertDatabaseCount('experiment_quality_inputs', count($this->qualityInputs));
        $this->assertDatabaseCount('experiment_quantitative_outputs', count($this->quantitativeOutputs));
        $this->assertDatabaseCount('experiment_quality_outputs', count($this->qualityOutputs));
    }

    public function test_unauthorized_user_cannot_store_experiment(): void
    {
        $unauthorizedUser = User::factory()->create();

        $response = $this->actingAs($unauthorizedUser)
            ->postJson(route('research.experiments.store', [
                'research' => $this->research,
            ]), $this->experimentData);

        $response->assertForbidden();
        $this->assertDatabaseCount('experiments', 0);
    }

    public function test_unauthenticated_user_cannot_store_experiment(): void
    {
        $response = $this->postJson(route('research.experiments.store', [
            'research' => $this->research,
        ]), $this->experimentData);

        $response->assertUnauthorized();
        $this->assertDatabaseCount('experiments', 0);
    }

    public function test_user_cannot_store_experiment_when_research_does_not_exist(): void
    {
        $researchId = Research::max('id') + 1;

        $response = $this->actingAs($this->user)
            ->postJson(route('research.experiments.store', [
                'research' => $researchId,
            ]), $this->experimentData);

        $response->assertNotFound();
        $this->assertDatabaseCount('experiments', 0);
    }

    public function test_user_cannot_store_experiment_without_required_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('research.experiments.store', [
                'research' => $this->research,
            ]), [
                'quantitative_inputs' => $this->quantitativeInputs,
                'quality_inputs' => $this->qualityInputs,
                'quantitative_outputs' => $this->quantitativeOutputs,
                'quality_outputs' => $this->qualityOutputs,
            ]);

        $response->assertInvalid(['name', 'date']);
        $this->assertDatabaseCount('experiments', 0);
    }
}
