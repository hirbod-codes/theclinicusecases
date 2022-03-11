<?php

namespace TheClinicUseCases\Accounts\Interfaces;

use TheClinicDataStructures\DataStructures\User\DSUser;

interface IDataBaseRetrieveAccounts
{
    /**
     * @return \TheClinic\DataStructures\User\DSUser[]
     */
    public function getAccounts(?int $lastVisitId = null, int $count, string $ruleName): array;

    public function getAccount(int $Id, string $ruleName): DSUser;
}
