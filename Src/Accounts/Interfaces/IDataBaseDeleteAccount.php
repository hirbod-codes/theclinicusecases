<?php

namespace TheClinicUseCases\Accounts\Interfaces;

use TheClinicDataStructures\DataStructures\User\DSUser;

interface IDataBaseDeleteAccount
{
    public function deleteAccount(DSUser $user): void;
}
