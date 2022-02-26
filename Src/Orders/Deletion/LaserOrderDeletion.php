<?php

namespace TheClinicUseCases\Orders\Deletion;

use TheClinicDataStructures\DataStructures\Order\Laser\DSLaserOrder;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Orders\Interfaces\IDataBaseDeleteLaserOrder;
use TheClinicUseCases\Privileges\PrivilegesManagement;

class LaserOrderDeletion
{
    private Authentication $authentication;

    private PrivilegesManagement $privilegesManagement;

    public function __construct(
        Authentication $authentication = null,
        PrivilegesManagement $privilegesManagement = null,
    ) {
        $this->authentication = $authentication ?: new Authentication;
        $this->privilegesManagement = $privilegesManagement ?: new PrivilegesManagement;
    }

    public function deleteLaserOrder(DSLaserOrder $laserOrder, DSUser $user, IDataBaseDeleteLaserOrder $db): void
    {
        if ($user->getId() === $laserOrder->getUser()->getId()) {
            $privilege = "selfLaserOrderDelete";
        } else {
            $privilege = "laserOrderDelete";
        }

        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, $privilege);

        $db->deleteLaserOrder($laserOrder, $user);
    }
}
