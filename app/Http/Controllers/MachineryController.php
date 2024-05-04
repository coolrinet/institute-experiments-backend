<?php

namespace App\Http\Controllers;

use App\Http\Requests\Machinery\StoreMachineryRequest;
use App\Http\Requests\Machinery\UpdateMachineryRequest;
use App\Http\Resources\MachineryResource;
use App\Models\Machinery;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class MachineryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): ResourceCollection
    {
        $name = $request->query('name');
        $userId = $request->query('user_id');

        $machineries = Machinery::with('user');

        if ($name) {
            $machineries = $machineries->filterByName($name);
        }

        if ($userId) {
            $machineries = $machineries->filterByUserId($userId);
        }

        return MachineryResource::collection($machineries->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMachineryRequest $request): Response
    {
        $machinery = new Machinery();
        $machinery->fill($request->validated());
        $machinery->user()->associate($request->user());
        $machinery->save();

        return response()->noContent(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Machinery $machinery): MachineryResource
    {
        return MachineryResource::make($machinery);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMachineryRequest $request, Machinery $machinery): Response
    {
        $machinery->fill($request->validated());

        $machinery->save();

        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Machinery $machinery): Response
    {
        Gate::authorize('delete', $machinery);

        $machinery->delete();

        return response()->noContent();
    }
}
