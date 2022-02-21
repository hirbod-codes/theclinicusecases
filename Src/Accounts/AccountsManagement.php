<?php

namespace TheClinicUseCases\Accounts;

use TheClinic\DataStructures\User\DSUser;
use TheClinicUseCases\Accounts\Interfaces\IDataBaseRetrieveAccounts;
use TheClinicUseCases\Exceptions\PrivilegeNotFound;
use TheClinicUseCases\Exceptions\Accounts\UserIsNotAuthenticated;
use TheClinicUseCases\Exceptions\Accounts\UserIsNotAuthorized;

class AccountsManagement
{
    /**
     * @return \TheClinic\DataStructures\User\DSUser[]
     */
    public function getAccounts(int $lastVisitId, int $count, DSUser $user, IDataBaseRetrieveAccounts $db): array
    {
        $this->checkAccountsReadPrivilege($user);

        return $db->getAccounts($lastVisitId, $count);
    }

    public function checkAccountsReadPrivilege(DSUser $user): void
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
}
