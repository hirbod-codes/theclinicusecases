<?php

namespace TheClinicUseCases\Privileges\Interfaces;

interface IDataBaseDeleteRole
{
    public function deleteRole(string $customRoleName): void;
}
