<?php

namespace TheClinicUseCases\Orders;

use TheClinic\DataStructures\Order\DSOrders;
use TheClinic\DataStructures\Order\DSLaserOrders;
use TheClinic\DataStructures\User\DSUser;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveLaserOrders;
use TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveOrders;
use TheClinicUseCases\Privileges\PrivilegesManagement;

class OrderManagement
{
    private Authentication $authentication;

    private PrivilegesManagement $privilegesManagement;

    public function __construct(
        Authentication $authentication,
        PrivilegesManagement $privilegesManagement
    ) {
        $this->authentication = $authentication;
        $this->privilegesManagement = $privilegesManagement;
    }

    public function getOrders(DSUser $user, int $lastOrderId = null, int $count, IDataBaseRetrieveOrders $db): DSOrders
    {
        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, "regularOrdersRead");

        return $db->getOrders($lastOrderId, $count);
    }

    public function getLaserOrders(DSUser $user, int $lastLaserOrderId = null, int $count, IDataBaseRetrieveLaserOrders $db): DSLaserOrders
    {
        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, "laserOrdersRead");

        return $db->getLaserOrders($lastLaserOrderId, $count);
    }
}
