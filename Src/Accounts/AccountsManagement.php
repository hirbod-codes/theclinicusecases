<?php

namespace TheClinicUseCases\Accounts;

use TheClinicDataStructures\DataStructures\User\DSAdmin;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicUseCases\Accounts\Interfaces\IDataBaseCreateAccount;
use TheClinicUseCases\Accounts\Interfaces\IDataBaseDeleteAccount;
use TheClinicUseCases\Accounts\Interfaces\IDataBaseRetrieveAccounts;
use TheClinicUseCases\Accounts\Interfaces\IDataBaseUpdateAccount;
use TheClinicUseCases\Exceptions\Accounts\AdminTemptsToDeleteAdminException;
use TheClinicUseCases\Exceptions\Accounts\AdminTemptsToUpdateAdminException;
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
     * @return \TheClinicDataStructures\DataStructures\User\DSUser[]
     */
    public function getAccounts(int $lastAccountId = null, int $count, string $ruleName, DSUser $user, IDataBaseRetrieveAccounts $db): array
    {
        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, "accountsRead");

        return $db->getAccounts($count, $ruleName, $lastAccountId);
    }

    public function getAccount(int $accountId, string $ruleName, DSUser $user, IDataBaseRetrieveAccounts $db): DSUser
    {
        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, "accountRead");

        return $db->getAccount($accountId, $ruleName);
    }

    public function getSelfAccount(string $ruleName, DSUser $user, IDataBaseRetrieveAccounts $db): DSUser
    {
        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, "selfAccountRead");

        return $db->getAccount($user->getId(), $ruleName);
    }

    public function createAccount(array $input, DSUser $user, IDataBaseCreateAccount $db): DSUser
    {
        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, "accountCreate");

        return $db->createAccount($input);
    }

    public function signupAccount(array $input, IDataBaseCreateAccount $db, ICheckForAuthenticatedUsers $iCheckForAuthenticatedUsers): DSUser
    {
        if (!$iCheckForAuthenticatedUsers->checkIfThereIsNoAuthenticated()) {
            throw new \RuntimeException("You're already loged in !!!", 500);
        }

        return $db->createAccount($input);
    }

    public function deleteAccount(DSUser $targetUser, DSUser $user, IDataBaseDeleteAccount $db): void
    {
        if ($user instanceof DSAdmin && $targetUser instanceof DSAdmin && $user->getId() !== $targetUser->getId()) {
            throw new AdminTemptsToDeleteAdminException();
        }

        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, "accountDelete");

        $db->deleteAccount($targetUser);
    }

    public function deleteSelfAccount(DSUser $user, IDataBaseDeleteAccount $db): void
    {
        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, "selfAccountDelete");

        $db->deleteAccount($user);
    }

    public function updateAccount(array $input, DSUser $targetUser, DSUser $user, IDataBaseUpdateAccount $db): DSUser
    {
        if ($user instanceof DSAdmin && $targetUser instanceof DSAdmin && $user->getId() !== $targetUser->getId()) {
            throw new AdminTemptsToUpdateAdminException();
        }

        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, "accountUpdate");

        return $db->massUpdateAccount($input, $targetUser);
    }

    public function updateSelfAccount(string $attribute, mixed $newValue, DSUser $user, IDataBaseUpdateAccount $db): DSUser
    {
        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, "selfAccountUpdate");
        $this->privilegesManagement->checkBool($user, "selfAccountUpdate" . ucfirst($attribute));

        return $db->updateAccount($attribute, $newValue, $user);
    }
}
