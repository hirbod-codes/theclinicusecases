<?php

namespace TheClinicUseCases\Traits;

use TheClinicDataStructures\DataStructures\User\DSAdmin;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicUseCases\Exceptions\AdminModificationByUserException;
use TheClinicUseCases\Exceptions\AdminsCollisionException;

trait TraitGetPrivilegeFromInput
{
    /**
     * @param DSUser $user
     * @param DSUser $targetUser
     * @param string $selfPrivilegeName
     * @param string $privilegeName
     * @return string
     * 
     * @throws AdminsCollisionException
     */
    private function getPrivilegeFromInput(DSUser $user, DSUser $targetUser, string $selfPrivilegeName, string $privilegeName): string
    {
        if ($user instanceof DSAdmin) {
            if ($user->getId() === $targetUser->getId()) {
                $privilege = $selfPrivilegeName;
            } elseif ($targetUser instanceof DSAdmin) {
                throw new AdminsCollisionException();
            } else {
                $privilege = $privilegeName;
            }
        } elseif ($targetUser instanceof DSAdmin) {
            throw new AdminModificationByUserException();
        } else {
            if ($user->getId() === $targetUser->getId()) {
                $privilege = $selfPrivilegeName;
            } else {
                $privilege = $privilegeName;
            }
        }

        return $privilege;
    }
}
