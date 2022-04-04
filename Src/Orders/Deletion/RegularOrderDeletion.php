<?php

namespace TheClinicUseCases\Orders\Deletion;

use TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrder;
use TheClinicDataStructures\DataStructures\User\DSAdmin;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Orders\Interfaces\IDataBaseDeleteRegularOrder;
use TheClinicUseCases\Traits\TraitGetPrivilegeFromInput;
use TheClinicUseCases\Privileges\PrivilegesManagement;

class RegularOrderDeletion
{
    use TraitGetPrivilegeFromInput;

    private Authentication $authentication;

    private PrivilegesManagement $privilegesManagement;

    public function __construct(
        Authentication $authentication = null,
        PrivilegesManagement $privilegesManagement = null,
    ) {
        $this->authentication = $authentication ?: new Authentication;
        $this->privilegesManagement = $privilegesManagement ?: new PrivilegesManagement;
    }

    public function deleteRegularOrder(DSRegularOrder $regularOrder, DSUser $targetUser, DSUser $user, IDataBaseDeleteRegularOrder $db): void
    {
        $this->authentication->check($user);

        $privilege = $this->getPrivilegeFromInput($user, $targetUser, "selfRegularOrderDelete", "regularOrderDelete");

        $this->privilegesManagement->checkBool($user, $privilege);

        $db->deleteRegularOrder($regularOrder, $targetUser);
    }
}
