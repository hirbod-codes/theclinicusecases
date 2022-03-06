<?php

namespace TheClinicUseCases\Accounts\Interfaces;

use TheClinicDataStructures\DataStructures\User\DSUser;

interface IDataBaseCreateAccount
{
    public function createAccount(DSUser $newUser, string $password): DSUser;
}
