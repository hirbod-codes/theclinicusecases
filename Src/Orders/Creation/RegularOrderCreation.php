<?php

namespace TheClinicUseCases\Orders\Creation;

use TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrder;
use TheClinicDataStructures\DataStructures\User\DSAdmin;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Orders\Interfaces\IDataBaseCreateDefaultRegularOrder;
use TheClinicUseCases\Orders\Interfaces\IDataBaseCreateRegularOrder;
use TheClinicUseCases\Traits\TraitGetPrivilegeFromInput;
use TheClinicUseCases\Privileges\PrivilegesManagement;

class RegularOrderCreation
{
    use TraitGetPrivilegeFromInput;

    private Authentication $authentication;

    private PrivilegesManagement $privilegesManagement;

    public function __construct(
        Authentication $authentication = null,
        PrivilegesManagement $privilegesManagement = null
    ) {
        $this->authentication = $authentication ?: new Authentication;
        $this->privilegesManagement = $privilegesManagement ?: new PrivilegesManagement;
    }

    public function createRegularOrder(
        int $price,
        int $timeConsumption,
        DSUser $targetUser,
        DSAdmin $user,
        IDataBaseCreateRegularOrder $db,
    ): DSRegularOrder {
        $privilege = $this->getPrivilegeFromInput($user, $targetUser, "selfRegularOrderCreate", "regularOrderCreate");

        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, $privilege);

        return $db->createRegularOrder($targetUser, $price, $timeConsumption);
    }

    public function createDefaultRegularOrder(DSUser $targetUser, DSUser $user, IDataBaseCreateDefaultRegularOrder $db): DSRegularOrder
    {
        $privilege = $this->getPrivilegeFromInput($user, $targetUser, "selfRegularOrderCreate", "regularOrderCreate");

        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, $privilege);

        return $db->createDefaultRegularOrder($targetUser);
    }
}
