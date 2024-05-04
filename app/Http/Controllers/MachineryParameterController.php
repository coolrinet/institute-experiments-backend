<?php

namespace App\Http\Controllers;

use App\Http\Requests\MachineryParameter\StoreMachineryParameterRequest;
use App\Http\Resources\MachineryParameterResource;
use App\Models\MachineryParameter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

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
        $machineryParameter = new MachineryParameter();

        $machineryParameter->fill($request->safe()->except('machinery_id'));
        if ($request->has('machinery_id')) {
            $machineryParameter->machinery()->associate($request->machinery_id);
        }
        $machineryParameter->user()->associate($request->user());
        $machineryParameter->save();

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
    public function update(Request $request, MachineryParameter $machineryParameter)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MachineryParameter $machineryParameter)
    {
        //
    }
}
