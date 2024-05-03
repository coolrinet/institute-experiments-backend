<?php

namespace App\Http\Controllers;

use App\Http\Requests\Machinery\StoreMachineryRequest;
use App\Http\Requests\Machinery\UpdateMachineryRequest;
use App\Http\Resources\MachineryResource;
use App\Models\Machinery;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MachineryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): ResourceCollection
    {
        $relation = $request->query('include');

        if ($relation) {
            abort_if($relation !== 'user', 404);

            return MachineryResource::collection(Machinery::with($relation)->paginate());
        }

        return MachineryResource::collection(Machinery::paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMachineryRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Machinery $machinery)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMachineryRequest $request, Machinery $machinery)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Machinery $machinery)
    {
        //
    }
}
