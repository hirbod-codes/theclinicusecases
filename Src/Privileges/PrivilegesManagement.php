<?php

namespace TheClinicUseCases\Privileges;

use TheClinicDataStructures\DataStructures\User\DSAdmin;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Exceptions\Accounts\AdminTemptsToSetAdminPrivilege;
use TheClinicUseCases\Exceptions\Accounts\UserIsNotAuthorized;
use TheClinicUseCases\Exceptions\PrivilegeNotFound;

class PrivilegesManagement
{
    private Authentication $authentication;

    public function __construct(
        Authentication|null $authentication = null
    ) {
        $this->authentication = $authentication ?: new Authentication;
    }

    /**
     * @param DSAdmin $user
     * @return string[]
     */
    public function getPrivileges(DSAdmin $user): array
    {
        $this->authentication->check($user);

        return DSUser::getPrivileges();
    }

    public function getUserPrivileges(DSAdmin $readerUser, DSUser $targetUser): array
    {
        $this->authentication->check($readerUser);

        return $targetUser::getUserPrivileges();
    }

    public function getSelfPrivileges(DSUser $user): array
    {
        $this->authentication->check($user);

        return $user::getUserPrivileges();
    }

    public function setUserPrivilege(DSAdmin $user, DSUser $targetUser, string $privilege, mixed $value): void
    {
        $this->authentication->check($user);

        if ($targetUser instanceof DSAdmin) {
            throw new AdminTemptsToSetAdminPrivilege();
        }

        $targetUser->setPrivilege($privilege, $value);
    }

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
