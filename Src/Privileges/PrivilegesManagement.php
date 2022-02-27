<?php

namespace TheClinicUseCases\Privileges;

use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicUseCases\Exceptions\Accounts\UserIsNotAuthorized;
use TheClinicUseCases\Exceptions\PrivilegeNotFound;

class PrivilegesManagement
{
    /**
     * Check for existence and truthiness of a privilege that has a boolean value.
     * 
     * @param \TheClinicDataStructures\DataStructures\User\DSUser $user
     * @param string $privilege
     * @return void
     * 
     * @throws \TheClinicUseCases\Exceptions\Accounts\UserIsNotAuthorized
     * @throws \TheClinicUseCases\Exceptions\PrivilegeNotFound
     */
    public function checkBool(DSUser $user, string $privilege): void
    {
        if ($user->privilegeExists($privilege) && $user->getPrivilege($privilege) === true) {
        } elseif (!$user->privilegeExists($privilege)) {
            throw new PrivilegeNotFound();
        } else {
            throw new UserIsNotAuthorized();
        }
    }
}
