<?php

namespace TheClinicUseCases\Visits\Interfaces;

use TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrder;
use TheClinicDataStructures\DataStructures\Visit\Regular\DSRegularVisit;

interface IDataBaseCreateVisit
{
    public function createRegularVisit(DSRegularOrder $dsRegularOrder, int $timestamp): DSRegularVisit;

    public function createLaserVisit(DSRegularOrder $dsRegularOrder, int $timestamp): DSRegularVisit;
}
