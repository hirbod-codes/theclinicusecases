<?php

namespace TheClinicUseCases\Privileges;

use TheClinicDataStructures\DataStructures\User\DSAdmin;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicDataStructures\DataStructures\User\Interfaces\IPrivilege;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Exceptions\Accounts\AdminTemptsToSetAdminPrivilege;
use TheClinicUseCases\Exceptions\Accounts\UserIsNotAuthorized;
use TheClinicUseCases\Exceptions\PrivilegeNotFound;
use TheClinicUseCases\Privileges\Interfaces\IDataBaseCreateRole;
use TheClinicUseCases\Privileges\Interfaces\IDataBaseDeleteRole;

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

    public function setUserPrivilege(DSAdmin $user, DSUser $targetUser, string $privilege, mixed $value, IPrivilege $ip): void
    {
        $this->authentication->check($user);

        if ($targetUser instanceof DSAdmin) {
            throw new AdminTemptsToSetAdminPrivilege();
        }

        $targetUser->setPrivilege($privilege, $value, $ip);
    }

    public function createRole(DSAdmin $user, string $customRoleName, array $privilegeValue, IDataBaseCreateRole $iDataBaseCreateRole): void
    {
        $this->authentication->check($user);

        $iDataBaseCreateRole->createRole($customRoleName, $privilegeValue);
    }

    public function deleteRole(DSAdmin $user, string $customRoleName, IDataBaseDeleteRole $iDataBaseDeleteRole): void
    {
        if (in_array($customRoleName, DSUser::$roles)) {
            throw new \InvalidArgumentException('Ypu can not delete this role, this is a business role.', 403);
        }

        $this->authentication->check($user);

        $iDataBaseDeleteRole->deleteRole($customRoleName);
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
