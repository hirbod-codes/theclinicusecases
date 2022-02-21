<?php

namespace TheClinicUseCases\Orders;

use TheClinic\DataStructures\Order\DSOrders;
use TheClinic\DataStructures\Order\DSLaserOrders;
use TheClinic\DataStructures\User\DSUser;
use TheClinicUseCases\Exceptions\Accounts\UserIsNotAuthenticated;
use TheClinicUseCases\Exceptions\Accounts\UserIsNotAuthorized;
use TheClinicUseCases\Exceptions\PrivilegeNotFound;
use TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveLaserOrders;
use TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveOrders;

class OrderManagement
{
    public function getOrders(DSUser $user, int $lastOrderId = null, int $count, IDataBaseRetrieveOrders $db): DSOrders
    {
        $this->checkOrdersReadPrivilege($user);

        return $db->getOrders($lastOrderId, $count);
    }

    private function checkOrdersReadPrivilege(DSUser $user): void
    {
        if (!$user->isAuthenticated()) {
            throw new UserIsNotAuthenticated();
        }

        $role = $user->getRole();

        if ($role->privilegeExists("ordersRead") && $role->getPrivilegeValue("ordersRead") === true) {
        } elseif (!$role->privilegeExists("ordersRead")) {
            throw new PrivilegeNotFound();
        } else {
            throw new UserIsNotAuthorized();
        }
    }

    public function getLaserOrders(DSUser $user, int $lastLaserOrderId = null, int $count, IDataBaseRetrieveLaserOrders $db): DSLaserOrders
    {
        $this->checkLaserOrdersReadPrivilege($user);

        return $db->getLaserOrders($lastLaserOrderId, $count);
    }

    private function checkLaserOrdersReadPrivilege(DSUser $user): void
    {
        if (!$user->isAuthenticated()) {
            throw new UserIsNotAuthenticated();
        }

        $role = $user->getRole();

        if ($role->privilegeExists("laserOrdersRead") && $role->getPrivilegeValue("laserOrdersRead") === true) {
        } elseif (!$role->privilegeExists("laserOrdersRead")) {
            throw new PrivilegeNotFound();
        } else {
            throw new UserIsNotAuthorized();
        }
    }
}
