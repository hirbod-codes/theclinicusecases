<?php

namespace TheClinicUseCases\Privileges;

use TheClinic\DataStructures\User\DSUser;
use TheClinicUseCases\Exceptions\Accounts\UserIsNotAuthorized;
use TheClinicUseCases\Exceptions\PrivilegeNotFound;

class PrivilegesManagement
{
    /**
     * Check for existence and truthiness of a privilege that has a boolean value.
     * 
     * @param DSUser $user
     * @param string $privilege
     * @return void
     * @throws UserIsNotAuthorized
     * @throws PrivilegeNotFound
     */
    public function checkBool(DSUser $user, string $privilege): void
    {
        $role = $user->getRule();

        if ($role->privilegeExists($privilege) && $role->getPrivilegeValue($privilege) === true) {
        } elseif (!$role->privilegeExists($privilege)) {
            throw new PrivilegeNotFound();
        } else {
            throw new UserIsNotAuthorized();
        }
    }
}
