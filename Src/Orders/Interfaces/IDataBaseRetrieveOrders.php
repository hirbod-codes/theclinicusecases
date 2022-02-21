<?php

namespace TheClinicUseCases\Orders\Interfaces;

use TheClinic\DataStructures\Order\DSOrders;

interface IDataBaseRetrieveOrders
{
    public function getOrders(int $lastOrderId = null, int $count): DSOrders;
}
