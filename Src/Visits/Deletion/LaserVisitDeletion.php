<?php

namespace TheClinicUseCases\Visits\Deletion;

use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicDataStructures\DataStructures\Visit\Laser\DSLaserVisit;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Privileges\PrivilegesManagement;
use TheClinicUseCases\Visits\Interfaces\IDataBaseDeleteVisit;

class LaserVisitDeletion
{
    private Authentication $authentication;

    private PrivilegesManagement $privilegesManagement;

    public function __construct(
        Authentication $authentication,
        PrivilegesManagement $privilegesManagement
    ) {
        $this->authentication = $authentication;
        $this->privilegesManagement = $privilegesManagement;
    }

    public function delete(DSLaserVisit $dsLaserVisit, DSUser $user, IDataBaseDeleteVisit $db): void
    {
        if ($dsLaserVisit->getOrder()->getUser()->getId() === $user->getId()) {
            $privilege = "selfLaserVisitDelete";
        } else {
            $privilege = "laserVisitDelete";
        }

        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, $privilege);

        $db->deleteLaserVisit($dsLaserVisit);
    }
}
