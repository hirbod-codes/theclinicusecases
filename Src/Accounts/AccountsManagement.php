<?php

namespace TheClinicUseCases\Accounts;

use TheClinicUseCases\Accounts\Interfaces\IDataBaseRetrieveAccounts;

class AccountsManagement
{
    /**
     * @return \TheClinic\DataStructures\User\DSUser[]
     */
    public function getAccounts(int $lastVisitId, int $count, IDataBaseRetrieveAccounts $db): array
    {
        return $db->getAccounts($lastVisitId, $count);
    }
}
