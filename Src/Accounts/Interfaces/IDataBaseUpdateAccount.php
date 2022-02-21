<?php

namespace TheClinicUseCases\Accounts\Interfaces;

interface IDataBaseUpdateAccount
{
    public function updateAccount(array $input): void;
}
