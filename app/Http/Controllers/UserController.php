<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): ResourceCollection
    {
        $admins = $request->query('admins');
        $email = $request->query('email');

        $users = User::when($admins, function ($query) {
            return $query->where('is_admin', true);
        })
            ->when($email, function ($query) use ($email) {
                return $query->where('email', 'like', "%{$email}%");
            })
            ->paginate();

        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): Response
    {
        $password = Str::password();

        User::create(array_merge($request->validated(), [
            'password' => Hash::make($password),
        ]));

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
