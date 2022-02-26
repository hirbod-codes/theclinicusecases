<?php

namespace TheClinicUseCases\Orders\Deletion;

use TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrder;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Orders\Interfaces\IDataBaseDeleteRegularOrder;
use TheClinicUseCases\Privileges\PrivilegesManagement;

class RegularOrderDeletion
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

    public function deleteRegularOrder(DSRegularOrder $regularOrder, DSUser $user, IDataBaseDeleteRegularOrder $db): void
    {
        if ($user->getId() === $regularOrder->getUser()->getId()) {
            $privilege = "selfRegularOrderDelete";
        } else {
            $privilege = "regularOrderDelete";
        }

        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, $privilege);

        $db->deleteRegularOrder($regularOrder, $user);
    }
}
