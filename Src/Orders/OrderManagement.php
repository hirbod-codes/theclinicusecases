<?php

namespace TheClinicUseCases\Orders;

use TheClinic\DataStructures\Order\DSOrders;
use TheClinic\DataStructures\Order\DSLaserOrders;
use TheClinic\DataStructures\Order\DSOrder;
use TheClinic\DataStructures\User\DSUser;
use TheClinic\Order\ICalculateRegularOrder;
use TheClinic\Order\Regular\RegularOrder;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Orders\Interfaces\IDataBaseCreateRegularOrder;
use TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveLaserOrders;
use TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveOrders;
use TheClinicUseCases\Privileges\PrivilegesManagement;

class OrderManagement
{
    private Authentication $authentication;

    private PrivilegesManagement $privilegesManagement;

    private ICalculateRegularOrder $iCalculateRegularOrder;

    public function __construct(
        Authentication $authentication,
        PrivilegesManagement $privilegesManagement,
        ICalculateRegularOrder $iCalculateRegularOrder = null
    ) {
        $this->iCalculateRegularOrder = $iCalculateRegularOrder ?: new RegularOrder;
        
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

    public function createRegularOrder(DSUser $user, IDataBaseCreateRegularOrder $db): DSOrder
    {
        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, "regularOrderCreate");

        return $db->createRegularOrder($user, $this->iCalculateRegularOrder->calculatePrice(), $this->iCalculateRegularOrder->calculateTimeConsumption());
    }
}
