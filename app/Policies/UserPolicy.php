<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function before($user, $ability) {
        if (!$user->isUser()) {
            return true;
        }
    }

    protected function hasAccessToUser($currentUser, $user) {
        return $currentUser->id == $user->id;
    }

    /**
     * Determine whether the user can view the user.
     *
     * @param  \App\User  $user
     * @param  \App\User  $user
     * @return mixed
     */
    public function view(User $currentUser, User $user)
    {
        return $this->hasAccessToUser($currentUser, $user);
    }

    /**
     * Determine whether the user can create users.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the user.
     *
     * @param  \App\User  $currentUser
     * @param  \App\User  $user
     * @return mixed
     */
    public function update(User $currentUser, User $user)
    {
        return $this->hasAccessToUser($currentUser, $user);
    }

    /**
     * Determine whether the user can delete the user.
     *
     * @param  \App\User  $currentUser
     * @param  \App\User  $user
     * @return mixed
     */
    public function delete(User $currentUser, User $user)
    {
        return $this->hasAccessToUser($currentUser, $user);
    }
}
