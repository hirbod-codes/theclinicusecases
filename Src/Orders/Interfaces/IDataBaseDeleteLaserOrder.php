<?php

namespace TheClinicUseCases\Orders\Interfaces;

use TheClinicDataStructures\DataStructures\Order\Laser\DSLaserOrder;
use TheClinicDataStructures\DataStructures\User\DSUser;

interface IDataBaseDeleteLaserOrder
{
    public function deleteLaserOrder(DSLaserOrder $laserOrder, DSUser $user): void;
}
