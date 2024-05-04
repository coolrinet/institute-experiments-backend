<?php

namespace App\Http\Controllers;

use App\Http\Requests\MachineryParameter\StoreMachineryParameterRequest;
use App\Http\Requests\MachineryParameter\UpdateMachineryParameterRequest;
use App\Http\Resources\MachineryParameterResource;
use App\Models\MachineryParameter;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class MachineryParameterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): ResourceCollection
    {
        return MachineryParameterResource::collection(
            MachineryParameter::with(['machinery', 'user'])->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMachineryParameterRequest $request): Response
    {
        DB::transaction(function () use ($request) {
            $machineryParameter = new MachineryParameter();

            $machineryParameter->fill($request->safe()->except('machinery_id'));
            $machineryParameter->machinery()->associate($request->validated(['machinery_id']));
            $machineryParameter->user()->associate($request->user());
            $machineryParameter->save();
        });

        return response()->noContent(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(MachineryParameter $machineryParameter): MachineryParameterResource
    {
        return MachineryParameterResource::make(
            $machineryParameter->load('machinery')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateMachineryParameterRequest $request,
        MachineryParameter              $machineryParameter
    ): Response
    {
        DB::transaction(function () use ($request, $machineryParameter) {
            $machineryParameter->fill($request->safe()->except('machinery_id'));
            $machineryParameter->machinery()->associate($request->validated(['machinery_id']));
            $machineryParameter->save();
        });

        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MachineryParameter $machineryParameter)
    {
        //
    }
}
