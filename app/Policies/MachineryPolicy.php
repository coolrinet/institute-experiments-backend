<?php

namespace App\Policies;

use App\Models\Machinery;
use App\Models\User;

class MachineryPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Machinery $machinery): bool
    {
        return $machinery->user->id == $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Machinery $machinery): bool
    {
        return $machinery->user->id == $user->id;
    }
}
