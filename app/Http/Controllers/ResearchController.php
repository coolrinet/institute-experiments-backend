<?php

namespace App\Http\Controllers;

use App\Http\Requests\Research\StoreResearchRequest;
use App\Http\Requests\Research\UpdateResearchRequest;
use App\Http\Resources\ResearchResource;
use App\Models\Research;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ResearchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): ResourceCollection
    {
        $name = $request->query('name');
        $machineryId = $request->query('machinery_id');
        $authorId = $request->query('author_id');

        $research = Research::with(['author', 'machinery']);

        if ($name) {
            $research = $research->where('name', 'like', '%'.$name.'%');
        }

        if ($machineryId) {
            $research = $research->where('machinery_id', $machineryId);
        }

        if (is_null($authorId)) {
            $research = $research->where('is_public', true)
                ->orWhere('author_id', $request->user()->id);
        } elseif ($authorId == $request->user()->id) {
            $research = $research->whereAuthorId($authorId);
        } else {
            $research = $research->where('author_id', $authorId)
                ->where('is_public', true);
        }

        return ResearchResource::collection(
            $research->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreResearchRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Research $research)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateResearchRequest $request, Research $research)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Research $research)
    {
        //
    }
}
