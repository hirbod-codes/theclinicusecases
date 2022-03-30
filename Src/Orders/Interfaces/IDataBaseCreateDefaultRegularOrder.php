<?php

namespace TheClinicUseCases\Orders\Interfaces;

use TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrder;
use TheClinicDataStructures\DataStructures\User\DSUser;

interface IDataBaseCreateDefaultRegularOrder
{
    public function createDefaultRegularOrder(DSUser $user): DSRegularOrder;
}
