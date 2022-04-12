<?php

namespace TheClinicUseCases\Visits\Interfaces;

use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicDataStructures\DataStructures\Visit\Regular\DSRegularVisit;

interface IDataBaseDeleteRegularVisit extends IDataBaseDeleteVisit
{
    public function deleteRegularVisit(DSRegularVisit $dsRegularVisit, DSUser $targetUser): void;
}
