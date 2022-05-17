<?php

namespace TheClinicUseCases\Privileges\Interfaces;

interface IDataBaseCreateRole
{
    public function createRole(string $customRoleName, array $privilegeValue, string $relatedRole): void;
}
