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
        return UserResource::make($request->user());
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
            $user->machineryParameters()->exists()
            || $user->machineries()->exists()
            || $user->experiments()->exists()
            || $user->research()->exists(),
            Response::HTTP_CONFLICT,
            'You cannot delete your profile with related data.'
        );

        Auth::guard('web')->logout();

        DB::transaction(function () use ($user) {
            $user->participatoryResearch()->detach();
            $user->delete();
        });

        return response()->noContent();
    }
}
