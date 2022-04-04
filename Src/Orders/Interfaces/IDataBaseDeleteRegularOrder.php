<?php

namespace TheClinicUseCases\Orders\Interfaces;

use TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrder;
use TheClinicDataStructures\DataStructures\User\DSUser;

interface IDataBaseDeleteRegularOrder
{
    public function deleteRegularOrder(DSRegularOrder $regularOrder, DSUser $targetUser): void;
}
