<?php

namespace TheClinicUseCases\Visits\Interfaces;

use TheClinic\Visit\IFindVisit;
use TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrder;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicDataStructures\DataStructures\Visit\Regular\DSRegularVisit;

interface IDataBaseCreateRegularVisit extends IDataBaseCreateVisit
{
    public function createRegularVisit(
        DSRegularOrder $dsRegularOrder,
        DSUser $targetUser,
        IFindVisit $iFindVisit
    ): DSRegularVisit;
}
