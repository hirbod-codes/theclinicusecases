<?php

namespace TheClinicUseCases\Orders\Interfaces;

use TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrders;
use TheClinicDataStructures\DataStructures\User\DSUser;

interface IDataBaseRetrieveRegularOrders
{
    public function getRegularOrders(int $lastOrderId = null, int $count): DSRegularOrders;

    public function getSelfRegularOrders(DSUser $user): DSRegularOrders;
}
