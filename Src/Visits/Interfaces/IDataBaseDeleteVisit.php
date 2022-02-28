<?php

namespace TheClinicUseCases\Visits\Interfaces;

use TheClinicDataStructures\DataStructures\Visit\Laser\DSLaserVisit;
use TheClinicDataStructures\DataStructures\Visit\Regular\DSRegularVisit;

interface IDataBaseDeleteVisit
{
    public function deleteRegularVisit(DSRegularVisit $dsRegularVisit): void;

    public function deleteLaserVisit(DSLaserVisit $dsLaserVisit): void;
}
