<?php

namespace TheClinicUseCases\Visits\Interfaces;

use TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrder;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicDataStructures\DataStructures\Visit\Regular\DSRegularVisit;
use TheClinicDataStructures\DataStructures\Visit\Regular\DSRegularVisits;

interface IDataBaseRetrieveRegularVisits extends IDataBaseRetrieveVisits
{
    public function getVisitsByUser(DSUser $targetUser, string $sortByTimestamp): DSRegularVisits;

    public function getVisitsByOrder(DSUser $dsTargetUser, DSRegularOrder $dsRegularOrder, string $sortByTimestamp): DSRegularVisits;

    public function getVisitsByTimestamp(string $operator, int $timestamp, string $sortByTimestamp): DSRegularVisits;
}
