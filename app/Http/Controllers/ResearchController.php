<?php

namespace App\Http\Controllers;

use App\Http\Requests\Research\StoreResearchRequest;
use App\Http\Requests\Research\UpdateResearchRequest;
use App\Http\Resources\ResearchResource;
use App\Models\MachineryParameter;
use App\Models\Research;
use Illuminate\Database\Eloquent\Builder;
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
        $page = $request->query('page');

        $research = Research::with(['author', 'machinery'])
            ->where(function (Builder $query) use ($request) {
                $query->where('is_public', true)
                    ->orWhere('author_id', $request->user()->id)
                    ->orWhereRelation('participants', 'id', request()->user()->id);
            })
            ->when($name, function (Builder $query) use ($name) {
                $query->where('name', 'like', $name.'%');
            })
            ->when($machineryId, function (Builder $query) use ($machineryId) {
                $query->where('machinery_id', $machineryId);
            });

        return ResearchResource::collection(
            is_null($page) ? $research->get() : $research->paginate(5)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreResearchRequest $request): Response
    {
        DB::transaction(function () use ($request) {
            $research = new Research();

            $research->fill($request->safe()->except(['machinery_id', 'participants']));
            $research->machinery()->associate($request->validated('machinery_id'));
            $research->author()->associate($request->user());

            $research->save();

            $parameters = MachineryParameter::whereMachineryId($request->validated('machinery_id'))
                ->orWhere('machinery_id', null)
                ->pluck('id');

            $research->parameters()->attach($parameters);

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

        return ResearchResource::make(
            $research->load(['machinery', 'author', 'participants', 'parameters'])
                ->loadCount('experiments')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateResearchRequest $request, Research $research): Response
    {
        DB::transaction(function () use ($research, $request) {
            $research->fill($request->safe()->except(['machinery_id', 'participants']));

            $machineryId = $request->validated('machinery_id');

            if ($machineryId !== $research->machinery->id) {
                abort_if($research->experiments()->exists(), Response::HTTP_CONFLICT, 'Нельзя изменить установку, если в исследовании уже есть эксперименты');

                $research->machinery()->associate($request->validated('machinery_id'));

                $parameters = MachineryParameter::whereMachineryId($request->validated('machinery_id'))
                    ->orWhere('machinery_id', null)
                    ->pluck('id');

                $research->parameters()->sync($parameters);
            }

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
            'Нельзя удалить исследование с экспериментами'
        );

        DB::transaction(function () use ($research) {
            $research->participants()->detach();
            $research->parameters()->detach();
            $research->delete();
        });

        return response()->noContent();
    }
}
