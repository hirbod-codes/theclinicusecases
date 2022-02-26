<?php

namespace TheClinicUseCases\Orders\Interfaces;

use TheClinicDataStructures\DataStructures\Order\Laser\DSLaserOrder;
use TheClinicDataStructures\DataStructures\Order\Laser\DSLaserOrders;
use TheClinicDataStructures\DataStructures\User\DSUser;

interface IDataBaseRetrieveLaserOrders
{
    public function getLaserOrders(int $lastOrderId = null, int $count): DSLaserOrders;
    
    public function getLaserOrder(DSUser $targetUser): DSLaserOrder;
}
