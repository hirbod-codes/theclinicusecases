<?php

namespace TheClinicUseCases\Orders\Interfaces;

use TheClinicDataStructures\DataStructures\Order\DSLaserOrders;

interface IDataBaseRetrieveLaserOrders
{
    public function getLaserOrders(int $lastOrderId = null, int $count): DSLaserOrders;
}
