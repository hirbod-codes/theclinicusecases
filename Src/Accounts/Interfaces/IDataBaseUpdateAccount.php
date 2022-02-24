<?php

namespace TheClinicUseCases\Accounts\Interfaces;

use TheClinic\DataStructures\User\DSUser;

interface IDataBaseUpdateAccount
{
    public function updateAccount(array $input): DSUser;
}
