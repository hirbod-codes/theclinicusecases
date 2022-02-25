<?php

namespace TheClinicUseCases\Orders\Interfaces;

use TheClinicDataStructures\DataStructures\Order\DSOrder;
use TheClinicDataStructures\DataStructures\User\DSUser;

interface IDataBaseCreateRegularOrder
{
    public function createRegularOrder(DSUser $user, int $price, int $timeConsumption): DSOrder;
}
