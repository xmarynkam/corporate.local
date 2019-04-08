<?php

namespace Corp\Policies;

use Corp\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermissionPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    // Need to work with the new permission EDIT_PERMISSIONS
    // As an example EDIT_USERS
    public function change(User $user) {
        return $user->canDo('EDIT_USERS');
    }
}
