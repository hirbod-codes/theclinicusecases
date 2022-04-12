<?php

namespace TheClinicUseCases\Visits\Interfaces;

use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicDataStructures\DataStructures\Visit\Laser\DSLaserVisit;

interface IDataBaseDeleteLaserVisit extends IDataBaseDeleteVisit
{
    public function deleteLaserVisit(DSLaserVisit $dsLaserVisit, DSUser $targetUser): void;
}
