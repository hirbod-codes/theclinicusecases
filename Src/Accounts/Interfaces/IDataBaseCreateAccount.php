<?php

namespace TheClinicUseCases\Accounts\Interfaces;

use TheClinicDataStructures\DataStructures\User\DSUser;

interface IDataBaseCreateAccount
{
    public function createAccount(array $newUser): DSUser;
}
