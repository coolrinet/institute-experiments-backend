<?php

namespace Tests\Feature\MachineryParameter;

use App\Enums\MachineryParameterTypeEnum;
use App\Enums\MachineryParameterValueTypeEnum;
use App\Models\Machinery;
use App\Models\MachineryParameter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateMachineryParameterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->machineryParameter = MachineryParameter::factory()
            ->recycle($this->user)
            ->create([
                'name' => 'Test machinery parameter',
                'parameter_type' => MachineryParameterTypeEnum::INPUT,
                'value_type' => MachineryParameterValueTypeEnum::QUANTITATIVE,
                'machinery_id' => null,
            ]);

        $this->machineryId = Machinery::factory()->create()->id;
    }

    public function test_authenticated_user_can_update_machinery_parameter(): void
    {
        $response = $this->actingAs($this->user)
            ->putJson(route('machinery-parameters.update', $this->machineryParameter), [
                'name' => 'Updated machinery parameter',
                'parameter_type' => MachineryParameterTypeEnum::OUTPUT,
                'value_type' => MachineryParameterValueTypeEnum::QUANTITATIVE,
                'machinery_id' => $this->machineryId,
            ]);

        $response->assertNoContent();
        $this->assertDatabaseHas('machinery_parameters', [
            'name' => 'Updated machinery parameter',
            'parameter_type' => MachineryParameterTypeEnum::OUTPUT,
            'value_type' => MachineryParameterValueTypeEnum::QUANTITATIVE,
            'machinery_id' => $this->machineryId,
        ]);
    }

    public function test_user_cannot_update_another_user_machinery_parameter(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->putJson(route('machinery-parameters.update', $this->machineryParameter), [
                'name' => 'Updated machinery parameter',
                'parameter_type' => MachineryParameterTypeEnum::OUTPUT,
                'value_type' => MachineryParameterValueTypeEnum::QUANTITATIVE,
                'machinery_id' => $this->machineryId,
            ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('machinery_parameters', [
            'name' => 'Test machinery parameter',
            'parameter_type' => MachineryParameterTypeEnum::INPUT,
            'value_type' => MachineryParameterValueTypeEnum::QUANTITATIVE,
            'machinery_id' => null,
        ]);
    }

    public function test_user_cannot_update_machinery_parameter_without_name(): void
    {
        $response = $this->actingAs($this->user)
            ->putJson(route('machinery-parameters.update', $this->machineryParameter), [
                'parameter_type' => MachineryParameterTypeEnum::OUTPUT,
                'value_type' => MachineryParameterValueTypeEnum::QUANTITATIVE,
                'machinery_id' => $this->machineryId,
            ]);

        $response->assertUnprocessable();
        $response->assertInvalid('name');
        $this->assertDatabaseHas('machinery_parameters', [
            'name' => 'Test machinery parameter',
            'parameter_type' => MachineryParameterTypeEnum::INPUT,
            'value_type' => MachineryParameterValueTypeEnum::QUANTITATIVE,
            'machinery_id' => null,
        ]);
    }

    public function test_user_cannot_update_machinery_parameter_with_nonunique_name(): void
    {
        MachineryParameter::factory()->create([
            'name' => 'Updated machinery parameter',
        ]);

        $response = $this->actingAs($this->user)
            ->putJson(route('machinery-parameters.update', $this->machineryParameter), [
                'name' => 'Updated machinery parameter',
                'parameter_type' => MachineryParameterTypeEnum::OUTPUT,
                'value_type' => MachineryParameterValueTypeEnum::QUANTITATIVE,
                'machinery_id' => $this->machineryId,
            ]);

        $response->assertUnprocessable();
        $response->assertInvalid('name');
        $this->assertDatabaseHas('machinery_parameters', [
            'name' => 'Test machinery parameter',
            'parameter_type' => MachineryParameterTypeEnum::INPUT,
            'value_type' => MachineryParameterValueTypeEnum::QUANTITATIVE,
            'machinery_id' => null,
        ]);
    }

    public function test_user_cannot_update_machinery_parameter_without_parameter_type_and_value_type(): void
    {
        $response = $this->actingAs($this->user)
            ->putJson(route('machinery-parameters.update', $this->machineryParameter), [
                'name' => 'Updated machinery parameter',
                'machinery_id' => $this->machineryId,
            ]);

        $response->assertUnprocessable();
        $response->assertInvalid(['parameter_type', 'value_type']);
        $this->assertDatabaseHas('machinery_parameters', [
            'name' => 'Test machinery parameter',
            'parameter_type' => MachineryParameterTypeEnum::INPUT,
            'value_type' => MachineryParameterValueTypeEnum::QUANTITATIVE,
            'machinery_id' => null,
        ]);
    }

    public function test_unauthenticated_user_cannot_update_machinery_parameter(): void
    {
        $response = $this->putJson(route('machinery-parameters.update', $this->machineryParameter), [
            'name' => 'Updated machinery parameter',
            'parameter_type' => MachineryParameterTypeEnum::OUTPUT,
            'value_type' => MachineryParameterValueTypeEnum::QUANTITATIVE,
            'machinery_id' => $this->machineryId,
        ]);

        $response->assertUnauthorized();
        $this->assertDatabaseHas('machinery_parameters', [
            'name' => 'Test machinery parameter',
            'parameter_type' => MachineryParameterTypeEnum::INPUT,
            'value_type' => MachineryParameterValueTypeEnum::QUANTITATIVE,
            'machinery_id' => null,
        ]);
    }

    public function test_user_cannot_update_machinery_parameter_that_does_not_exist(): void
    {
        $id = MachineryParameter::max('id') + 1;

        $response = $this->actingAs($this->user)
            ->putJson(route('machinery-parameters.update', $id), [
                'name' => 'Updated machinery parameter',
                'parameter_type' => MachineryParameterTypeEnum::OUTPUT,
                'value_type' => MachineryParameterValueTypeEnum::QUANTITATIVE,
                'machinery_id' => $this->machineryId,
            ]);

        $response->assertNotFound();
    }
}
