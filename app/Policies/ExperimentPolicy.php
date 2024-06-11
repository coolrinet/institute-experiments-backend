<?php

namespace App\Policies;

use App\Models\Experiment;
use App\Models\Research;
use App\Models\User;

class ExperimentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, int $researchId): bool
    {
        $research = Research::find($researchId);

        return $research->is_public
            || $user->is($research->author)
            || $research->has('participants') && $research->participants->contains($user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Experiment $experiment): bool
    {
        $research = $experiment->research;

        return $research->is_public
            || $user->is($research->author)
            || $research->has('participants') && $research->participants->contains($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, int $researchId): bool
    {
        $research = Research::find($researchId);

        return $user->is($research->author)
            || $research->has('participants') && $research->participants->contains($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Experiment $experiment): bool
    {
        return $user->is($experiment->user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Experiment $experiment): bool
    {
        return $user->is($experiment->user);
    }
}
