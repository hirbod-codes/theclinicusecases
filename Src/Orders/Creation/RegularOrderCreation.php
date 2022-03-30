<?php

namespace TheClinicUseCases\Orders\Creation;

use TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrder;
use TheClinicDataStructures\DataStructures\User\DSAdmin;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Exceptions\Orders\AdminTemptsToCreateOrderForAdminException;
use TheClinicUseCases\Exceptions\Orders\UserTemptsToCreateOrderForAdminException;
use TheClinicUseCases\Orders\Interfaces\IDataBaseCreateDefaultRegularOrder;
use TheClinicUseCases\Orders\Interfaces\IDataBaseCreateRegularOrder;
use TheClinicUseCases\Privileges\PrivilegesManagement;

class RegularOrderCreation
{
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
        if ($targetUser->getId() === $user->getId()) {
            $privilege = "selfRegularOrderCreate";
        } else {
            if ($targetUser instanceof DSAdmin) {
                throw new AdminTemptsToCreateOrderForAdminException();
            }
            $privilege = "regularOrderCreate";
        }

        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, $privilege);

        return $db->createRegularOrder($targetUser, $price, $timeConsumption);
    }

    public function createDefaultRegularOrder(DSUser $targetUser, DSUser $user, IDataBaseCreateDefaultRegularOrder $db): DSRegularOrder
    {
        if ($targetUser->getId() === $user->getId()) {
            $privilege = "selfRegularOrderCreate";
        } else {
            if ($targetUser instanceof DSAdmin) {
                if ($user instanceof DSAdmin) {
                    throw new AdminTemptsToCreateOrderForAdminException();
                } else {
                    throw new UserTemptsToCreateOrderForAdminException();
                }
            }
            $privilege = "regularOrderCreate";
        }

        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, $privilege);

        return $db->createDefaultRegularOrder($targetUser);
    }
}
