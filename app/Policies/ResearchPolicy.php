<?php

namespace App\Policies;

use App\Models\Research;
use App\Models\User;

class ResearchPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Research $research): bool
    {
        return $research->is_public
            || $user->is($research->author)
            || $research->participants->contains($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Research $research): bool
    {
        return $user->is($research->author)
            || $research->participants->contains($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Research $research): bool
    {
        return $user->is($research->author);
    }
}
