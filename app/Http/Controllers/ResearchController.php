<?php

namespace App\Http\Controllers;

use App\Http\Requests\Research\StoreResearchRequest;
use App\Http\Requests\Research\UpdateResearchRequest;
use App\Http\Resources\ResearchResource;
use App\Models\Research;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

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
        $page = $request->query('page');

        $research = Research::with(['author', 'machinery']);

        if ($name) {
            $research = $research->where('name', 'like', '%' . $name . '%');
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

        if ($page) {
            $research = $research->paginate(5);
        } else {
            $research = $research->get();
        }

        return ResearchResource::collection(
            $research
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreResearchRequest $request): Response
    {
        DB::transaction(function () use ($request) {
            $research = new Research();

            $research->fill($request->safe()->except(['machinery_id', 'participants', 'parameters']));
            $research->machinery()->associate($request->validated('machinery_id'));
            $research->author()->associate($request->user());

            $research->save();

            $research->parameters()->attach($request->validated('parameters'));

            if ($request->has('participants')) {
                $research->participants()->attach($request->validated('participants'));
            }
        });

        return response()->noContent(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Research $research): ResearchResource
    {
        Gate::authorize('view', $research);

        return ResearchResource::make($research->load(['machinery', 'author', 'participants']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateResearchRequest $request, Research $research): Response
    {
        DB::transaction(function () use ($research, $request) {
            $research->fill($request->safe()->except(['machinery_id', 'participants', 'parameters']));
            $research->machinery()->associate($request->validated('machinery_id'));
            $research->parameters()->sync($request->validated('parameters'));

            if ($request->has('participants')) {
                $research->participants()->sync($request->validated('participants'));
            } else {
                $research->participants()->detach($request->validated('participants'));
            }

            $research->save();
        });

        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Research $research): Response
    {
        Gate::authorize('delete', $research);

        abort_if(
            $research->experiments()->exists(),
            Response::HTTP_CONFLICT,
            'Cannot delete research with experiments'
        );

        DB::transaction(function () use ($research) {
            $research->participants()->detach();
            $research->parameters()->detach();
            $research->delete();
        });

        return response()->noContent();
    }
}
