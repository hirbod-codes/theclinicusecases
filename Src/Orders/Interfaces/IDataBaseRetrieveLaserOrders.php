<?php

namespace TheClinicUseCases\Orders\Interfaces;

use TheClinic\DataStructures\Order\DSLaserOrders;

interface IDataBaseRetrieveLaserOrders
{
    public function getLaserOrders(int $lastOrderId = null, int $count): DSLaserOrders;
}
