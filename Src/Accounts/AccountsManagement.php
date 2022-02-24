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
use TheClinicUseCases\Privileges\PrivilegesManagement;

class AccountsManagement
{
    private Authentication $authentication;

    private PrivilegesManagement $privilegesManagement;

    public function __construct(
        Authentication $authentication,
        PrivilegesManagement $privilegesManagement
    ) {
        $this->authentication = $authentication;
        $this->privilegesManagement = $privilegesManagement;
    }

    /**
     * @param integer $lastAccountId
     * @param integer $count
     * @param DSUser $user
     * @param IDataBaseRetrieveAccounts $db
     * @return \TheClinic\DataStructures\User\DSUser[]
     */
    public function getAccounts(int $lastAccountId = null, int $count, DSUser $user, IDataBaseRetrieveAccounts $db): array
    {
        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, "accountsRead");

        return $db->getAccounts($lastAccountId, $count);
    }

    public function getSelfAccounts(DSUser $user, IDataBaseRetrieveAccounts $db): DSUser
    {
        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, "selfAccountRead");

        return $db->getAccount($user->getId());
    }

    public function createAccount(array $input, DSUser $user, IDataBaseCreateAccount $db): DSUser
    {
        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, "accountCreate");

        return $db->createAccount($input);
    }

    public function deleteAccount(DSUser $targetUser, DSUser $user, IDataBaseDeleteAccount $db): void
    {
        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, "accountDelete");

        $db->deleteAccount($targetUser);
    }

    public function updateAccount(array $input, DSUser $user, IDataBaseUpdateAccount $db): DSUser
    {
        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, "accountUpdate");

        return $db->updateAccount($input);
    }
}
