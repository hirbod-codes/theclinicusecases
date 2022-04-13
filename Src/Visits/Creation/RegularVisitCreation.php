<?php

namespace TheClinicUseCases\Visits\Creation;

use TheClinic\Visit\IFindVisit;
use TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrder;
use TheClinicDataStructures\DataStructures\Time\DSWeekDaysPeriods;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicDataStructures\DataStructures\Visit\Regular\DSRegularVisit;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Traits\TraitGetPrivilegeFromInput;
use TheClinicUseCases\Privileges\PrivilegesManagement;
use TheClinicUseCases\Visits\Interfaces\IDataBaseCreateRegularVisit;

class RegularVisitCreation
{
    use TraitGetPrivilegeFromInput;

    private Authentication $authentication;

    private PrivilegesManagement $privilegesManagement;

    public function __construct(
        Authentication $authentication = null,
        PrivilegesManagement $privilegesManagement = null
    ) {
        $this->authentication = $authentication ?: new Authentication;
        $this->privilegesManagement = $privilegesManagement ?: new PrivilegesManagement;
    }

    public function create(DSRegularOrder $dsRegularOrder, DSUser $targetUser, DSUser $user, IDataBaseCreateRegularVisit $db, IFindVisit $iFindVisit): DSRegularVisit
    {
        $privilege = $this->getPrivilegeFromInput($user, $targetUser, "selfRegularVisitCreate", "regularVisitCreate");

        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, $privilege);

        return $db->createRegularVisit($dsRegularOrder, $targetUser, $iFindVisit);
    }
}
