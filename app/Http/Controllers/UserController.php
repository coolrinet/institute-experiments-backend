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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): ResourceCollection
    {
        $users = User::paginate(5);

        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): Response
    {
        $password = Str::password();

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
        abort_if(
            $user->experiments()->exists(),
            Response::HTTP_CONFLICT,
            'Cannot delete user with experiments'
        );

        abort_if(
            $user->machineries()->exists(),
            Response::HTTP_CONFLICT,
            'Cannot delete user with machineries'
        );

        abort_if(
            $user->research()->exists(),
            Response::HTTP_CONFLICT,
            'Cannot delete user with research'
        );

        abort_if(
            $user->machineryParameters()->exists(),
            Response::HTTP_CONFLICT,
            'Cannot delete user with machinery parameters'
        );

        DB::transaction(function () use ($user) {
            $user->participatoryResearch()->detach();
            $user->delete();
        });

        return response()->noContent();
    }
}
