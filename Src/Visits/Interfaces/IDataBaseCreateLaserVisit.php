<?php

namespace TheClinicUseCases\Visits\Interfaces;

use TheClinic\Visit\IFindVisit;
use TheClinicDataStructures\DataStructures\Order\Laser\DSLaserOrder;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicDataStructures\DataStructures\Visit\Laser\DSLaserVisit;

interface IDataBaseCreateLaserVisit extends IDataBaseCreateVisit
{
    public function createLaserVisit(
        DSLaserOrder $dsLaserOrder,
        DSUser $targetUser,
        IFindVisit $iFindVisit
    ): DSLaserVisit;
}
