<?php

namespace Tests\Feature\MachineryParameter;

use App\Enums\MachineryParameterTypeEnum;
use App\Enums\MachineryParameterValueTypeEnum;
use App\Models\Machinery;
use App\Models\MachineryParameter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreMachineryParameterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_authenticated_user_can_store_machinery_parameter(): void
    {
        $machinery = Machinery::factory()->create();

        $response = $this->actingAs($this->user)
            ->postJson(route('machinery-parameters.store'), [
                'name' => 'Test machinery parameter',
                'parameter_type' => MachineryParameterTypeEnum::INPUT,
                'value_type' => MachineryParameterValueTypeEnum::QUANTITATIVE,
                'machinery_id' => $machinery->id,
            ]);

        $response->assertCreated();
        $this->assertDatabaseHas('machinery_parameters', [
            'name' => 'Test machinery parameter',
            'parameter_type' => MachineryParameterTypeEnum::INPUT,
            'value_type' => MachineryParameterValueTypeEnum::QUANTITATIVE,
            'machinery_id' => $machinery->id,
        ]);
    }

    public function test_authenticated_user_can_store_machinery_parameter_without_machinery(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('machinery-parameters.store'), [
                'name' => 'Test machinery parameter',
                'parameter_type' => MachineryParameterTypeEnum::INPUT,
                'value_type' => MachineryParameterValueTypeEnum::QUANTITATIVE,
            ]);

        $response->assertCreated();
        $this->assertDatabaseHas('machinery_parameters', [
            'name' => 'Test machinery parameter',
            'parameter_type' => MachineryParameterTypeEnum::INPUT,
            'value_type' => MachineryParameterValueTypeEnum::QUANTITATIVE,
        ]);
    }

    public function test_user_cannot_store_machinery_parameter_without_name(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('machinery-parameters.store'), [
                'parameter_type' => MachineryParameterTypeEnum::INPUT,
                'value_type' => MachineryParameterValueTypeEnum::QUANTITATIVE,
            ]);

        $response->assertUnprocessable();
        $response->assertInvalid('name');
    }

    public function test_user_cannot_store_machinery_parameter_with_same_name(): void
    {
        MachineryParameter::factory()->create(['name' => 'Test machinery parameter']);

        $response = $this->actingAs($this->user)
            ->postJson(route('machinery-parameters.store'), [
                'name' => 'Test machinery parameter',
                'parameter_type' => MachineryParameterTypeEnum::INPUT,
                'value_type' => MachineryParameterValueTypeEnum::QUANTITATIVE,
            ]);

        $response->assertUnprocessable();
        $response->assertInvalid('name');
    }

    public function test_user_cannot_store_machinery_parameter_without_parameter_type_and_value_type(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('machinery-parameters.store'), [
                'name' => 'Test machinery parameter',
            ]);

        $response->assertUnprocessable();
        $response->assertInvalid(['parameter_type', 'value_type']);
    }

    public function test_unauthenticated_user_cannot_store_machinery_parameter(): void
    {
        $response = $this->postJson(route('machinery-parameters.store'), [
            'name' => 'Test machinery parameter',
            'parameter_type' => MachineryParameterTypeEnum::INPUT,
            'value_type' => MachineryParameterValueTypeEnum::QUANTITATIVE,
        ]);

        $response->assertUnauthorized();
        $this->assertDatabaseMissing('machinery_parameters', [
            'name' => 'Test machinery parameter',
            'parameter_type' => MachineryParameterTypeEnum::INPUT,
            'value_type' => MachineryParameterValueTypeEnum::QUANTITATIVE,
        ]);
    }
}
