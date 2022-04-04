<?php

namespace TheClinicUseCases\Visits\Creation;

use TheClinic\Visit\IFindVisit;
use TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrder;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicDataStructures\DataStructures\Visit\Regular\DSRegularVisit;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Traits\TraitGetPrivilegeFromInput;
use TheClinicUseCases\Privileges\PrivilegesManagement;
use TheClinicUseCases\Visits\Interfaces\IDataBaseCreateVisit;

class RegularVisitCreation
{
    use TraitGetPrivilegeFromInput;

    private Authentication $authentication;

    private PrivilegesManagement $privilegesManagement;

    private IFindVisit $iFindVisit;

    public function __construct(
        Authentication $authentication = null,
        PrivilegesManagement $privilegesManagement = null,
        IFindVisit $iFindVisit
    ) {
        $this->authentication = $authentication ?: new Authentication;
        $this->privilegesManagement = $privilegesManagement ?: new PrivilegesManagement;

        $this->iFindVisit = $iFindVisit;
    }

    public function create(DSRegularOrder $dsRegularOrder, DSUser $targetUser, DSUser $user, IDataBaseCreateVisit $db): DSRegularVisit
    {
        $privilege = $this->getPrivilegeFromInput($user, $targetUser, "selfRegularVisitCreate", "regularVisitCreate");

        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, $privilege);

        return $db->createRegularVisit($dsRegularOrder, $targetUser, $this->iFindVisit->findVisit());
    }
}
