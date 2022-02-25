<?php

namespace TheClinicUseCases\Orders\Interfaces;

use TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrders;

interface IDataBaseRetrieveRegularOrders
{
    public function getRegularOrders(int $lastOrderId = null, int $count): DSRegularOrders;
}
