<?php

namespace TheClinicUseCases\Orders\Interfaces;

use TheClinic\DataStructures\Order\DSOrder;
use TheClinic\DataStructures\User\DSUser;

interface IDataBaseCreateRegularOrder
{
    public function createRegularOrder(DSUser $user, int $price, int $timeConsumption): DSOrder;
}
