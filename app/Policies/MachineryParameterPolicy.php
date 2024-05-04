<?php

namespace App\Policies;

use App\Models\MachineryParameter;
use App\Models\User;

class MachineryParameterPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MachineryParameter $machineryParameter): bool
    {
        return $user->id === $machineryParameter->user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MachineryParameter $machineryParameter): bool
    {
        return $user->id === $machineryParameter->user->id;
    }
}
