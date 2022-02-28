<?php

namespace TheClinicUseCases\Visits\Deletion;

use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicDataStructures\DataStructures\Visit\Regular\DSRegularVisit;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Privileges\PrivilegesManagement;
use TheClinicUseCases\Visits\Interfaces\IDataBaseDeleteVisit;

class RegularVisitDeletion
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

    public function delete(DSRegularVisit $dsRegularVisit, DSUser $user, IDataBaseDeleteVisit $db): void
    {
        if ($dsRegularVisit->getOrder()->getUser()->getId() === $user->getId()) {
            $privilege = "selfRegularVisitDelete";
        } else {
            $privilege = "regularVisitDelete";
        }

        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, $privilege);

        $db->deleteRegularVisit($dsRegularVisit);
    }
}
