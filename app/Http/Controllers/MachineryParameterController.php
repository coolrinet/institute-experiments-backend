<?php

namespace App\Http\Controllers;

use App\Http\Resources\MachineryParameterResource;
use App\Models\MachineryParameter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(MachineryParameter $machineryParameter)
    {
        //
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
