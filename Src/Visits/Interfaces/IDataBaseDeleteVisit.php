<?php

namespace TheClinicUseCases\Visits\Interfaces;

use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicDataStructures\DataStructures\Visit\Laser\DSLaserVisit;
use TheClinicDataStructures\DataStructures\Visit\Regular\DSRegularVisit;

interface IDataBaseDeleteVisit
{
    public function deleteRegularVisit(DSRegularVisit $dsRegularVisit, DSUser $targetUser): void;

    public function deleteLaserVisit(DSLaserVisit $dsLaserVisit, DSUser $targetUser): void;
}
