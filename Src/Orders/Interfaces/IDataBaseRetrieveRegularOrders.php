<?php

namespace TheClinicUseCases\Orders\Interfaces;

use TheClinicDataStructures\DataStructures\Order\DSOrders;

interface IDataBaseRetrieveRegularOrders
{
    public function getRegularOrders(int $lastOrderId = null, int $count): DSOrders;
}
