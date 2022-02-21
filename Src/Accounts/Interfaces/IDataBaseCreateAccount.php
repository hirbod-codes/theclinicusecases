<?php

namespace TheClinicUseCases\Accounts\Interfaces;

interface IDataBaseCreateAccount
{
    public function createAccount(array $input): void;
}
