<?php

namespace TheClinicUseCases\Orders\Interfaces;

use TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrder;
use TheClinicDataStructures\DataStructures\User\DSUser;

interface IDataBaseCreateRegularOrder
{
    public function createRegularOrder(DSUser $targetUser, int $price, int $timeConsumption): DSRegularOrder;
}
