<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the given profile.
     *
     * @param User $user
     * @param User $signedInUser
     * @return bool
     */
    public function update(user $user, User $signedInUser)
    {
        return $signedInUser->id === $user->id;
    }
}
