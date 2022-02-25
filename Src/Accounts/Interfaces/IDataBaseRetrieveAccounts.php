<?php

namespace TheClinicUseCases\Accounts\Interfaces;

use TheClinicDataStructures\DataStructures\User\DSUser;

interface IDataBaseRetrieveAccounts
{
    /**
     * @return \TheClinic\DataStructures\User\DSUser[]
     */
    public function getAccounts(int $lastVisitId, int $count): array;

    public function getAccount(int $Id): DSUser;
}
