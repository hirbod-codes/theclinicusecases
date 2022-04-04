<?php

namespace TheClinicUseCases\Visits\Interfaces;

use TheClinicDataStructures\DataStructures\Order\Laser\DSLaserOrder;
use TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrder;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicDataStructures\DataStructures\Visit\Laser\DSLaserVisit;
use TheClinicDataStructures\DataStructures\Visit\Regular\DSRegularVisit;

interface IDataBaseCreateVisit
{
    public function createRegularVisit(DSRegularOrder $dsRegularOrder, DSUser $targetUser, int $timestamp): DSRegularVisit;

    public function createLaserVisit(DSLaserOrder $dsLaserOrder, DSUser $targetUser, int $timestamp): DSLaserVisit;
}
