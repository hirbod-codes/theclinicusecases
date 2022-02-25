<?php

namespace TheClinicUseCases\Accounts;

use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicUseCases\Exceptions\Accounts\UserIsNotAuthenticated;

class Authentication
{
    /**
     * @param \TheClinicDataStructures\DataStructures\User\DSUser $user
     * @return void
     * @throws \TheClinicUseCases\Exceptions\Accounts\UserIsNotAuthenticated
     */
    public function check(DSUser $user): void
    {
        if (!$user->isAuthenticated()) {
            throw new UserIsNotAuthenticated();
        }
    }
}
