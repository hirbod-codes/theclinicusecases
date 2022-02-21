<?php

namespace TheClinicUseCases\Accounts\Interfaces;

use TheClinic\DataStructures\User\DSUser;

interface IDataBaseDeleteAccount
{
    public function deleteAccount(DSUser $user): void;
}
