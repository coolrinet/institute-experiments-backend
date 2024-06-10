<?php

namespace App\Http\Controllers;

use App\Http\Requests\MachineryParameter\StoreMachineryParameterRequest;
use App\Http\Requests\MachineryParameter\UpdateMachineryParameterRequest;
use App\Http\Resources\MachineryParameterResource;
use App\Models\MachineryParameter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class MachineryParameterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): ResourceCollection
    {
        $name = $request->query('name');
        $machineryId = $request->query('machinery_id');
        $parameterType = $request->query('parameter_type');
        $valueType = $request->query('value_type');
        $page = $request->query('page');

        $machineryParameters = MachineryParameter::with(['machinery', 'user'])
            ->when($name, function (Builder $query) use ($name) {
                $query->where('name', 'like', $name.'%');
            })
            ->when($machineryId, function (Builder $query) use ($machineryId) {
                $query->where('machinery_id', $machineryId);
            })
            ->when($parameterType, function (Builder $query) use ($parameterType) {
                $query->where('parameter_type', $parameterType);
            })
            ->when($valueType, function (Builder $query) use ($valueType) {
                $query->where('value_type', $valueType);
            });

        return MachineryParameterResource::collection(
            is_null($page) ? $machineryParameters->get() : $machineryParameters->paginate(5)
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
        MachineryParameter $machineryParameter
    ): Response {
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
    public function destroy(MachineryParameter $machineryParameter): Response
    {
        Gate::authorize('delete', $machineryParameter);

        abort_if(
            $machineryParameter->research()->exists(),
            Response::HTTP_CONFLICT,
            'Нельзя удалить параметр, который используется в исследованиях'
        );

        $machineryParameter->delete();

        return response()->noContent();
    }
}
