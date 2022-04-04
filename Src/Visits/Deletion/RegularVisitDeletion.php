<?php

namespace TheClinicUseCases\Visits\Deletion;

use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicDataStructures\DataStructures\Visit\Regular\DSRegularVisit;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Traits\TraitGetPrivilegeFromInput;
use TheClinicUseCases\Privileges\PrivilegesManagement;
use TheClinicUseCases\Visits\Interfaces\IDataBaseDeleteVisit;

class RegularVisitDeletion
{
    use TraitGetPrivilegeFromInput;

    private Authentication $authentication;

    private PrivilegesManagement $privilegesManagement;

    public function __construct(
        Authentication $authentication,
        PrivilegesManagement $privilegesManagement
    ) {
        $this->authentication = $authentication;
        $this->privilegesManagement = $privilegesManagement;
    }

    public function delete(DSRegularVisit $dsRegularVisit, DSUser $targetUser, DSUser $user, IDataBaseDeleteVisit $db): void
    {
        $privilege = $this->getPrivilegeFromInput($user, $targetUser, "selfRegularVisitDelete", "regularVisitDelete");

        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, $privilege);

        $db->deleteRegularVisit($dsRegularVisit, $targetUser);
    }
}
