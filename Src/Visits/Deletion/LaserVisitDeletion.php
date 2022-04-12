<?php

namespace TheClinicUseCases\Visits\Deletion;

use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicDataStructures\DataStructures\Visit\Laser\DSLaserVisit;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Traits\TraitGetPrivilegeFromInput;
use TheClinicUseCases\Privileges\PrivilegesManagement;
use TheClinicUseCases\Visits\Interfaces\IDataBaseDeleteLaserVisit;

class LaserVisitDeletion
{
    use TraitGetPrivilegeFromInput;

    private Authentication $authentication;

    private PrivilegesManagement $privilegesManagement;

    public function __construct(
        null|Authentication $authentication = null,
        null|PrivilegesManagement $privilegesManagement = null
    ) {
        $this->authentication = $authentication ?: new Authentication;
        $this->privilegesManagement = $privilegesManagement ?: new PrivilegesManagement;
    }

    public function delete(DSLaserVisit $dsLaserVisit, DSUser $targetUser, DSUser $user, IDataBaseDeleteLaserVisit $db): void
    {
        $privilege = $this->getPrivilegeFromInput($user, $targetUser, "selfLaserVisitDelete", "laserVisitDelete");

        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, $privilege);

        $db->deleteLaserVisit($dsLaserVisit, $targetUser);
    }
}
