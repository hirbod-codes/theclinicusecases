<?php

namespace TheClinicUseCases\Privileges\Interfaces;

interface IPrivilegeSetter
{
    public function setPrivilege(string $roleName, array $privilegeValues): void;
}
