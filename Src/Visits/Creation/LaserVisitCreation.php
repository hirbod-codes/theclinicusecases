<?php

namespace TheClinicUseCases\Visits\Creation;

use TheClinic\Visit\IFindVisit;
use TheClinicDataStructures\DataStructures\Order\Laser\DSLaserOrder;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicDataStructures\DataStructures\Visit\Laser\DSLaserVisit;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Traits\TraitGetPrivilegeFromInput;
use TheClinicUseCases\Privileges\PrivilegesManagement;
use TheClinicUseCases\Visits\Interfaces\IDataBaseCreateVisit;

class LaserVisitCreation
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

    public function create(DSLaserOrder $dsLaserOrder, DSUser $targetUser, DSUser $user, IDataBaseCreateVisit $db): DSLaserVisit
    {
        $privilege = $this->getPrivilegeFromInput($user, $targetUser, "selfLaserVisitCreate", "laserVisitCreate");

        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, $privilege);

        return $db->createLaserVisit($dsLaserOrder, $targetUser, $this->iFindVisit->findVisit());
    }
}
