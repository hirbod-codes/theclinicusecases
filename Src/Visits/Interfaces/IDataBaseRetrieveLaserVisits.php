<?php

namespace TheClinicUseCases\Visits\Interfaces;

use TheClinicDataStructures\DataStructures\Order\Laser\DSLaserOrder;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicDataStructures\DataStructures\Visit\Laser\DSLaserVisit;
use TheClinicDataStructures\DataStructures\Visit\Laser\DSLaserVisits;

interface IDataBaseRetrieveLaserVisits extends IDataBaseRetrieveVisits
{
    public function getVisitsByUser(DSUser $dsTargetUser, string $sortByTimestamp): DSLaserVisits;

    public function getVisitsByOrder(DSUser $dsTargetUser, DSLaserOrder $dsLaserOrder, string $sortByTimestamp): DSLaserVisits;

    public function getVisitsByTimestamp(string $operator, int $timestamp, string $sortByTimestamp): DSLaserVisits;
}
