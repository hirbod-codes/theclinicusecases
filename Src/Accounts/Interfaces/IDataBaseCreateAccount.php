<?php

namespace TheClinicUseCases\Accounts\Interfaces;

use TheClinic\DataStructures\User\DSUser;

interface IDataBaseCreateAccount
{
    public function createAccount(array $input): DSUser;
}
