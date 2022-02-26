<?php

namespace TheClinicUseCases\Orders\Retrieval;

use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Privileges\PrivilegesManagement;

class OrderRetrieval
{
    private Authentication $authentication;

    private PrivilegesManagement $privilegesManagement;

    public function __construct(
        Authentication $authentication,
        PrivilegesManagement $privilegesManagement,
    ) {
        $this->authentication = $authentication;
        $this->privilegesManagement = $privilegesManagement;
    }
}
