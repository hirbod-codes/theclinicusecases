<?php

namespace TheClinicUseCases\Orders\Interfaces;

use TheClinicDataStructures\DataStructures\Order\Laser\DSLaserOrders;

interface IDataBaseRetrieveLaserOrders
{
    public function getLaserOrders(int $lastOrderId = null, int $count): DSLaserOrders;
}
