<?php

namespace TheClinicUseCases\Accounts;

use TheClinic\DataStructures\User\DSUser;
use TheClinicUseCases\Exceptions\Accounts\UserIsNotAuthenticated;

class Authentication
{
    /**
     * @param DSUser $user
     * @return void
     * @throws UserIsNotAuthenticated
     */
    public function check(DSUser $user): void
    {
        if (!$user->isAuthenticated()) {
            throw new UserIsNotAuthenticated();
        }
    }
}
