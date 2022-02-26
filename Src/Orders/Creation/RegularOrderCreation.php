<?php

namespace TheClinicUseCases\Orders\Creation;

use TheClinic\Order\ICalculateRegularOrder;
use TheClinic\Order\Regular\RegularOrder;
use TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrder;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Orders\Interfaces\IDataBaseCreateRegularOrder;
use TheClinicUseCases\Privileges\PrivilegesManagement;

class RegularOrderCreation
{
    private Authentication $authentication;

    private PrivilegesManagement $privilegesManagement;

    private ICalculateRegularOrder $iCalculateRegularOrder;

    public function __construct(
        Authentication $authentication = null,
        PrivilegesManagement $privilegesManagement = null,
        ICalculateRegularOrder $iCalculateRegularOrder = null
    ) {
        $this->iCalculateRegularOrder = $iCalculateRegularOrder ?: new RegularOrder;

        $this->authentication = $authentication ?: new Authentication;
        $this->privilegesManagement = $privilegesManagement ?: new PrivilegesManagement;
    }

    public function createRegularOrder(DSUser $targetUser, DSUser $user, IDataBaseCreateRegularOrder $db): DSRegularOrder
    {
        if ($targetUser->getId() === $user->getId()) {
            $privilege = "selfRegularOrderCreate";
        } else {
            $privilege = "regularOrderCreate";
        }

        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, $privilege);

        return $db->createRegularOrder($targetUser, $this->iCalculateRegularOrder->calculatePrice(), $this->iCalculateRegularOrder->calculateTimeConsumption());
    }
}
