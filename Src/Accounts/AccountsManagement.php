<?php

namespace TheClinicUseCases\Accounts;

use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicUseCases\Accounts\Interfaces\IDataBaseCreateAccount;
use TheClinicUseCases\Accounts\Interfaces\IDataBaseDeleteAccount;
use TheClinicUseCases\Accounts\Interfaces\IDataBaseRetrieveAccounts;
use TheClinicUseCases\Accounts\Interfaces\IDataBaseUpdateAccount;
use TheClinicUseCases\Privileges\PrivilegesManagement;
use TheClinicUseCases\Traits\TraitGetPrivilegeFromInput;

class AccountsManagement
{
    use TraitGetPrivilegeFromInput;

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

    public function getAccount(string $targetUserUsername, DSUser $user, IDataBaseRetrieveAccounts $db): DSUser
    {
        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, "accountRead");

        $targetUser = $db->getAccount($targetUserUsername);

        $privilege = $this->getPrivilegeFromInput($user, $targetUser, "selfAccountRead", "accountRead");
        $this->privilegesManagement->checkBool($user, $privilege);

        return $targetUser;
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
        $privilege = $this->getPrivilegeFromInput($user, $targetUser, "selfAccountDelete", "accountDelete");

        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, $privilege);

        $db->deleteAccount($targetUser);
    }

    public function massUpdateAccount(array $input, DSUser $targetUser, DSUser $user, IDataBaseUpdateAccount $db): DSUser
    {
        $this->authentication->check($user);

        if (count($input) === 0) {
            throw new \InvalidArgumentException('$input is empty', 500);
        }

        foreach ($input as $attribute => $value) {
            if (!is_string($attribute)) {
                throw new \InvalidArgumentException('One of the attributes names in $input array is not a string', 500);
            }

            $privilege = $this->getPrivilegeFromInput($user, $targetUser, "selfAccountUpdate" . ucfirst($attribute), "accountUpdate" . ucfirst($attribute));

            $this->privilegesManagement->checkBool($user, $privilege);
        }

        return $db->massUpdateAccount($input, $targetUser);
    }

    public function updateAccount(string $attribute, mixed $newValue, DSUser $targetUser, DSUser $user, IDataBaseUpdateAccount $db): DSUser
    {
        $privilege = $this->getPrivilegeFromInput($user, $targetUser, "selfAccountUpdate" . ucfirst($attribute), "accountUpdate" . ucfirst($attribute));

        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, $privilege);

        return $db->updateAccount($attribute, $newValue, $targetUser);
    }
}
