<?php

namespace Tests\Feature\MachineryParameter;

use App\Enums\MachineryParameterType;
use App\Enums\MachineryParameterValueType;
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

    public function test_authenticated_user_can_filter_machinery_parameters_by_name(): void
    {
        $machineryParametersCount =
            MachineryParameter::filterByName(MachineryParameter::first()->name)->count();

        $response = $this->actingAs($this->user)
            ->getJson(
                route(
                    'machinery-parameters.index',
                    ['name' => MachineryParameter::first()->name]
                )
            );

        $response->assertOk();
        $response->assertJsonCount(min($machineryParametersCount, 15), 'data');
        $response->assertJsonPath('meta.total', $machineryParametersCount);
    }

    public function test_authenticated_user_can_filter_machinery_parameters_by_user_id(): void
    {
        $machineryParametersCount = MachineryParameter::filterByUserId($this->user->id)->count();

        $response = $this->actingAs($this->user)
            ->getJson(route('machinery-parameters.index', ['user_id' => $this->user->id]));

        $response->assertOk();
        $response->assertJsonCount(min($machineryParametersCount, 15), 'data');
        $response->assertJsonPath('meta.total', $machineryParametersCount);
    }

    public function test_authenticated_user_can_filter_machinery_parameters_by_machinery_id(): void
    {
        $machineryParametersBuilder =
            MachineryParameter::filterByMachineryId(
                MachineryParameter::where(
                    'machinery_id', '!=', null
                )
                    ->first()
                    ->machinery_id
            );

        $machineryParametersCount = $machineryParametersBuilder->count();

        $response = $this->actingAs($this->user)
            ->getJson(
                route(
                    'machinery-parameters.index',
                    ['machinery_id' => $machineryParametersBuilder->first()->machinery_id]
                )
            );

        $response->assertOk();
        $response->assertJsonCount(min($machineryParametersCount, 15), 'data');
        $response->assertJsonPath('meta.total', $machineryParametersCount);
    }

    public function test_authenticated_user_can_filter_machinery_parameters_by_parameter_type(): void
    {
        $machineryParametersCount =
            MachineryParameter::filterByParameterType(MachineryParameterType::INPUT->value)->count();

        $response = $this->actingAs($this->user)
            ->getJson(
                route('machinery-parameters.index',
                    ['parameter_type' => MachineryParameterType::INPUT->value])
            );

        $response->assertOk();
        $response->assertJsonCount(min($machineryParametersCount, 15), 'data');
        $response->assertJsonPath('meta.total', $machineryParametersCount);
    }

    public function test_authenticated_user_can_filter_machinery_parameters_by_value_type(): void
    {
        $machineryParametersCount =
            MachineryParameter::filterByValueType(MachineryParameterValueType::QUANTITATIVE->value)->count();

        $response = $this->actingAs($this->user)
            ->getJson(
                route('machinery-parameters.index',
                    ['value_type' => MachineryParameterValueType::QUANTITATIVE->value])
            );

        $response->assertOk();
        $response->assertJsonCount(min($machineryParametersCount, 15), 'data');
        $response->assertJsonPath('meta.total', $machineryParametersCount);
    }

    public function test_unauthenticated_user_cannot_get_list_of_machinery_parameters(): void
    {
        $response = $this->getJson(route('machinery-parameters.index'));

        $response->assertUnauthorized();
    }
}
