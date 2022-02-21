<?php

namespace TheClinicUseCases\Accounts;

use TheClinic\DataStructures\User\DSUser;
use TheClinicUseCases\Accounts\Interfaces\IDataBaseCreateAccount;
use TheClinicUseCases\Accounts\Interfaces\IDataBaseDeleteAccount;
use TheClinicUseCases\Accounts\Interfaces\IDataBaseRetrieveAccounts;
use TheClinicUseCases\Accounts\Interfaces\IDataBaseUpdateAccount;
use TheClinicUseCases\Exceptions\PrivilegeNotFound;
use TheClinicUseCases\Exceptions\Accounts\UserIsNotAuthenticated;
use TheClinicUseCases\Exceptions\Accounts\UserIsNotAuthorized;

class AccountsManagement
{
    /**
     * @param integer $lastAccountId
     * @param integer $count
     * @param DSUser $user
     * @param IDataBaseRetrieveAccounts $db
     * @return \TheClinic\DataStructures\User\DSUser[]
     */
    public function getAccounts(int $lastAccountId = null, int $count, DSUser $user, IDataBaseRetrieveAccounts $db): array
    {
        $this->checkAccountsReadPrivilege($user);

        return $db->getAccounts($lastAccountId, $count);
    }

    private function checkAccountsReadPrivilege(DSUser $user): void
    {
        if (!$user->isAuthenticated()) {
            throw new UserIsNotAuthenticated();
        }

        $role = $user->getRole();

        if ($role->privilegeExists("accountsRead") && $role->getPrivilegeValue("accountsRead") === true) {
        } elseif (!$role->privilegeExists("accountsRead")) {
            throw new PrivilegeNotFound();
        } else {
            throw new UserIsNotAuthorized();
        }
    }

    public function createAccount(array $input, DSUser $user, IDataBaseCreateAccount $db): void
    {
        $this->checkAccountCreatePrivilege($user);

        $db->createAccount($input);
    }

    private function checkAccountCreatePrivilege(DSUser $user): void
    {
        if (!$user->isAuthenticated()) {
            throw new UserIsNotAuthenticated();
        }

        $role = $user->getRole();

        if ($role->privilegeExists("accountCreate") && $role->getPrivilegeValue("accountCreate") === true) {
        } elseif (!$role->privilegeExists("accountCreate")) {
            throw new PrivilegeNotFound();
        } else {
            throw new UserIsNotAuthorized();
        }
    }

    public function deleteAccount(DSUser $targetUser, DSUser $user, IDataBaseDeleteAccount $db): void
    {
        $this->checkAccountDeletePrivilege($user);

        $db->deleteAccount($targetUser);
    }

    private function checkAccountDeletePrivilege(DSUser $user): void
    {
        if (!$user->isAuthenticated()) {
            throw new UserIsNotAuthenticated();
        }

        $role = $user->getRole();

        if ($role->privilegeExists("accountDelete") && $role->getPrivilegeValue("accountDelete") === true) {
        } elseif (!$role->privilegeExists("accountDelete")) {
            throw new PrivilegeNotFound();
        } else {
            throw new UserIsNotAuthorized();
        }
    }

    public function updateAccount(array $input, DSUser $user, IDataBaseUpdateAccount $db): void
    {
        $this->checkAccountUpdatePrivilege($user);

        $db->updateAccount($input);
    }

    private function checkAccountUpdatePrivilege(DSUser $user): void
    {
        if (!$user->isAuthenticated()) {
            throw new UserIsNotAuthenticated();
        }

        $role = $user->getRole();

        if ($role->privilegeExists("accountUpdate") && $role->getPrivilegeValue("accountUpdate") === true) {
        } elseif (!$role->privilegeExists("accountUpdate")) {
            throw new PrivilegeNotFound();
        } else {
            throw new UserIsNotAuthorized();
        }
    }
}
