<?php

namespace App\Http\Controllers;

use App\Http\Requests\Experiment\StoreExperimentRequest;
use App\Http\Requests\Experiment\UpdateExperimentRequest;
use App\Http\Resources\ExperimentResource;
use App\Models\Experiment;
use App\Models\Research;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ExperimentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Research $research, Request $request): ResourceCollection
    {
        Gate::authorize('viewAny', [Experiment::class, $research->id]);

        $page = $request->query('page');

        $experiments = $research->experiments()->with(['user']);

        return ExperimentResource::collection(
            is_null($page) ? $experiments->get() : $experiments->paginate(5),
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Research $research, StoreExperimentRequest $request): Response
    {
        DB::transaction(function () use ($research, $request) {
            $experiment = new Experiment();

            $experiment->fill($request->safe()->only(['name', 'date']));
            $experiment->research()->associate($research);
            $experiment->user()->associate($request->user());

            $experiment->save();

            $experiment->quantitativeInputs()->attach($request->validated('quantitative_inputs'));
            $experiment->qualityInputs()->attach($request->validated('quality_inputs'));
            $experiment->quantitativeOutputs()->attach($request->validated('quantitative_outputs'));
            $experiment->qualityOutputs()->attach($request->validated('quality_outputs'));
        });

        return response()->noContent(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Research $research, Experiment $experiment): ExperimentResource
    {
        Gate::authorize('view', [$experiment, $research->id]);

        return ExperimentResource::make($experiment->load([
            'user',
            'quantitativeInputs',
            'qualityInputs',
            'quantitativeOutputs',
            'qualityOutputs',
        ]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        Research $research,
        UpdateExperimentRequest $request,
        Experiment $experiment
    ): Response {
        DB::transaction(function () use ($request, $experiment) {
            $experiment->fill($request->safe()->only(['name', 'date']));

            $quantitativeInputs = $request->validated('quantitative_inputs');
            $qualityInputs = $request->validated('quality_inputs');
            $quantitativeOutputs = $request->validated('quantitative_outputs');
            $qualityOutputs = $request->validated('quality_outputs');

            foreach ($quantitativeInputs as $quantitativeInput) {
                $experiment->quantitativeInputs()->updateExistingPivot($quantitativeInput['parameter_id'], [
                    'value' => $quantitativeInput['value'],
                ]);
            }

            foreach ($qualityInputs as $qualityInput) {
                $experiment->qualityInputs()->updateExistingPivot($qualityInput['parameter_id'], [
                    'value' => $qualityInput['value'],
                ]);
            }

            foreach ($quantitativeOutputs as $quantitativeOutput) {
                $experiment->quantitativeOutputs()->updateExistingPivot($quantitativeOutput['parameter_id'], [
                    'value' => $quantitativeOutput['value'],
                ]);
            }

            foreach ($qualityOutputs as $qualityOutput) {
                $experiment->qualityOutputs()->updateExistingPivot($qualityOutput['parameter_id'], [
                    'value' => $qualityOutput['value'],
                ]);
            }

            $experiment->save();
        });

        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Research $research, Experiment $experiment): Response
    {
        Gate::authorize('delete', $experiment);

        DB::transaction(function () use ($experiment) {
            $experiment->quantitativeInputs()->detach();
            $experiment->qualityInputs()->detach();
            $experiment->quantitativeOutputs()->detach();
            $experiment->qualityOutputs()->detach();

            $experiment->delete();
        });

        return response()->noContent();
    }
}
