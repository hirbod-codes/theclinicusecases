<?php

namespace TheClinicUseCases\Accounts\Interfaces;

use TheClinicDataStructures\DataStructures\User\DSUser;

interface IDataBaseUpdateAccount
{
    public function updateAccount(array $input): DSUser;
}
