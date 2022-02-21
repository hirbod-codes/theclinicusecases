<?php

namespace TheClinicUseCases\Accounts\Interfaces;

interface IDataBaseRetrieveAccounts
{
    /**
     * @return \TheClinic\DataStructures\User\DSUser[]
     */
    public function getAccounts(int $lastVisitId, int $count):array;
}
