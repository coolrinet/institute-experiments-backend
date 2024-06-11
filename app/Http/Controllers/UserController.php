<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Mail\UserAdded;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): ResourceCollection
    {
        $page = $request->query('page');

        return UserResource::collection(
            is_null($page) ? User::all() : User::paginate(5)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): Response
    {
        Gate::authorize('create', User::class);

        $password = Str::password(12);

        $newUser = User::create(array_merge($request->validated(), [
            'password' => Hash::make($password),
        ]));

        Mail::to($newUser)->send(new UserAdded($newUser, $password));

        return response()->noContent(Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): Response
    {
        Gate::authorize('delete', $user);

        abort_if(
            $user->experiments()->exists(),
            Response::HTTP_CONFLICT,
            'Нельзя удалить пользователя, у которого имеются добавленные эксперименты'
        );

        abort_if(
            $user->machineries()->exists(),
            Response::HTTP_CONFLICT,
            'Нельзя удалить пользователя, у которого имеются добавленные установки'
        );

        abort_if(
            $user->research()->exists(),
            Response::HTTP_CONFLICT,
            'Нельзя удалить пользователя, у которого имеются добавленные исследования'
        );

        abort_if(
            $user->machineryParameters()->exists(),
            Response::HTTP_CONFLICT,
            'Нельзя удалить пользователя, у которого имеются добавленные параметры установок'
        );

        DB::transaction(function () use ($user) {
            $user->participatoryResearch()->detach();
            $user->delete();
        });

        return response()->noContent();
    }
}
