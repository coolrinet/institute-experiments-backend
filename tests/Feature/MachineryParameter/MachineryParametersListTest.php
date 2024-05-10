<?php

namespace Tests\Feature\MachineryParameter;

use App\Enums\MachineryParameterTypeEnum;
use App\Enums\MachineryParameterValueTypeEnum;
use App\Models\MachineryParameter;
use App\Models\User;
use Tests\TestCase;

class MachineryParametersListTest extends TestCase
{
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
        $machineryParameterName = MachineryParameter::first()->name;

        $machineryParametersCount =
            MachineryParameter::where('name', 'like', '%'.$machineryParameterName.'%')
                ->count();

        $response = $this->actingAs($this->user)
            ->getJson(
                route(
                    'machinery-parameters.index',
                    ['name' => $machineryParameterName]
                )
            );

        $response->assertOk();
        $response->assertJsonCount(min($machineryParametersCount, 15), 'data');
        $response->assertJsonPath('meta.total', $machineryParametersCount);
    }

    public function test_authenticated_user_can_filter_machinery_parameters_by_user_id(): void
    {
        $machineryParametersCount = MachineryParameter::whereUserId($this->user->id)->count();

        $response = $this->actingAs($this->user)
            ->getJson(route('machinery-parameters.index', ['user_id' => $this->user->id]));

        $response->assertOk();
        $response->assertJsonCount(min($machineryParametersCount, 15), 'data');
        $response->assertJsonPath('meta.total', $machineryParametersCount);
    }

    public function test_authenticated_user_can_filter_machinery_parameters_by_machinery_id(): void
    {
        $machineryParametersBuilder =
            MachineryParameter::whereMachineryId(
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
            MachineryParameter::whereParameterType(MachineryParameterTypeEnum::INPUT->value)->count();

        $response = $this->actingAs($this->user)
            ->getJson(
                route('machinery-parameters.index',
                    ['parameter_type' => MachineryParameterTypeEnum::INPUT->value])
            );

        $response->assertOk();
        $response->assertJsonCount(min($machineryParametersCount, 15), 'data');
        $response->assertJsonPath('meta.total', $machineryParametersCount);
    }

    public function test_authenticated_user_can_filter_machinery_parameters_by_value_type(): void
    {
        $machineryParametersCount =
            MachineryParameter::whereValueType(MachineryParameterValueTypeEnum::QUANTITATIVE->value)->count();

        $response = $this->actingAs($this->user)
            ->getJson(
                route('machinery-parameters.index',
                    ['value_type' => MachineryParameterValueTypeEnum::QUANTITATIVE->value])
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
