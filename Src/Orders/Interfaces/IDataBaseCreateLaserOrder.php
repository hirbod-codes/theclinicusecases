<?php

namespace TheClinicUseCases\Orders\Interfaces;

use TheClinic\DataStructures\Order\DSLaserOrder;
use TheClinic\DataStructures\Order\DSPackages;
use TheClinic\DataStructures\Order\DSParts;
use TheClinic\DataStructures\User\DSUser;

interface IDataBaseCreateLaserOrder
{
    public function createLaserOrder(DSUser $user, ?DSParts $parts = null, ?DSPackages $packages = null, int $price, int $timeConsumption, int $priceWithoutDiscount): DSLaserOrder;
}
