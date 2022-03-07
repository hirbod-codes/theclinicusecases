<?php

namespace TheClinicUseCases\Accounts\Interfaces;

use TheClinicDataStructures\DataStructures\User\DSUser;

interface IDataBaseUpdateAccount
{
    public function massUpdateAccount(array $input, DSUser $user): DSUser;
    public function updateAccount(string $attribute, mixed $newValue, DSUser $user): DSUser;
}
