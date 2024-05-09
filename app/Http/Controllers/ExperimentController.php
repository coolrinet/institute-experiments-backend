<?php

namespace App\Http\Controllers;

use App\Http\Requests\Experiment\StoreExperimentRequest;
use App\Http\Requests\Experiment\UpdateExperimentRequest;
use App\Http\Resources\ExperimentResource;
use App\Models\Experiment;
use App\Models\Research;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Gate;

class ExperimentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Research $research): ResourceCollection
    {
        Gate::authorize('viewAny', [Experiment::class, $research->id]);

        return ExperimentResource::collection(
            $research->experiments()
                ->with(['user'])
                ->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExperimentRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Research $research, Experiment $experiment): ExperimentResource
    {
        Gate::authorize('view', [$experiment, $research->id]);

        return ExperimentResource::make($experiment->load([
            'user',
            'research',
            'quantitativeInputs',
            'qualityInputs',
            'quantitativeOutputs',
            'qualityOutputs',
        ]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExperimentRequest $request, Experiment $experiment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Experiment $experiment)
    {
        //
    }
}
