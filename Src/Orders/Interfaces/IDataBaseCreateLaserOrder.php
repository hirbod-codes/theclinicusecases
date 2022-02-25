<?php

namespace TheClinicUseCases\Orders\Interfaces;

use TheClinicDataStructures\DataStructures\Order\DSPackages;
use TheClinicDataStructures\DataStructures\Order\DSParts;
use TheClinicDataStructures\DataStructures\Order\Laser\DSLaserOrder;
use TheClinicDataStructures\DataStructures\User\DSUser;

interface IDataBaseCreateLaserOrder
{
    public function createLaserOrder(DSUser $user, ?DSParts $parts = null, ?DSPackages $packages = null, int $price, int $timeConsumption, int $priceWithoutDiscount): DSLaserOrder;
}
