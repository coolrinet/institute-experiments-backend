<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Display a user profile.
     */
    public function showProfile(Request $request): UserResource
    {
        return UserResource::make(
            $request->user()
                ->loadCount(['machineries', 'machineryParameters', 'experiments', 'research', 'participatoryResearch'])
        );
    }

    /**
     * Update a user profile.
     */
    public function updateProfile(UpdateUserRequest $request): Response
    {
        $newData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'email' => $request->email,
        ];

        if ($request->has('password')) {
            $newData['password'] = Hash::make($request->current_password);
        }

        $request->user()->update($newData);

        return response()->noContent();
    }

    /**
     * Remove a user profile.
     */
    public function deleteProfile(Request $request): Response
    {
        $user = $request->user();

        abort_if(
            $user->experiments()->exists(),
            Response::HTTP_CONFLICT,
            'Нельзя удалить профиль, так как у вас имеются добавленные эксперименты'
        );

        abort_if(
            $user->machineries()->exists(),
            Response::HTTP_CONFLICT,
            'Нельзя удалить профиль, так как у вас имеются добавленные установки'
        );

        abort_if(
            $user->research()->exists(),
            Response::HTTP_CONFLICT,
            'Нельзя удалить профиль, так как у вас имеются добавленные исследования'
        );

        abort_if(
            $user->machineryParameters()->exists(),
            Response::HTTP_CONFLICT,
            'Нельзя удалить профиль, так как у вас имеются добавленные параметры установок'
        );

        Auth::guard('web')->logout();

        DB::transaction(function () use ($user) {
            $user->participatoryResearch()->detach();
            $user->delete();
        });

        return response()->noContent();
    }
}
