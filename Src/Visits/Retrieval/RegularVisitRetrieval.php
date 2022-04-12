<?php

namespace TheClinicUseCases\Visits\Retrieval;

use TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrder;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicDataStructures\DataStructures\Visit\Regular\DSRegularVisit;
use TheClinicDataStructures\DataStructures\Visit\Regular\DSRegularVisits;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Privileges\PrivilegesManagement;
use TheClinicUseCases\Traits\TraitGetPrivilegeFromInput;
use TheClinicUseCases\Visits\Interfaces\IDataBaseRetrieveRegularVisits;

class RegularVisitRetrieval
{
    use TraitGetPrivilegeFromInput;

    private Authentication $authentication;

    private PrivilegesManagement $privilegesManagement;

    public function __construct(
        null|Authentication $authentication = null,
        null|PrivilegesManagement $privilegesManagement = null
    ) {
        $this->authentication = $authentication ?: new Authentication;
        $this->privilegesManagement = $privilegesManagement ?: new PrivilegesManagement;
    }

    public function getVisitsByUser(DSUser $dsAuthenticated, DSUser $dsTargetUser, string $sortByTimestamp, IDataBaseRetrieveRegularVisits $db): DSRegularVisits
    {
        if (!in_array($sortByTimestamp, ['desc', 'asc'])) {
            throw new \InvalidArgumentException(
                '$sortByTimestamp variable must be one of the \'desc\' or \'asc\' values, \'' . $sortByTimestamp . '\' given.',
                500
            );
        }

        $privilege = $this->getPrivilegeFromInput($dsAuthenticated, $dsTargetUser, "selfRegularVisitRetrieve", "regularVisitRetrieve");

        $this->authentication->check($dsAuthenticated);
        $this->privilegesManagement->checkBool($dsAuthenticated, $privilege);

        return $db->getVisitsByUser($dsTargetUser, $sortByTimestamp);
    }

    public function getVisitsByOrder(DSUser $dsAuthenticated, DSUser $dsTargetUser, DSRegularOrder $dsRegularOrder, string $sortByTimestamp, IDataBaseRetrieveRegularVisits $db): DSRegularVisits
    {
        if (!in_array($sortByTimestamp, ['desc', 'asc'])) {
            throw new \InvalidArgumentException(
                '$sortByTimestamp variable must be one of the \'desc\' or \'asc\' values, \'' . $sortByTimestamp . '\' given.',
                500
            );
        }

        $privilege = $this->getPrivilegeFromInput($dsAuthenticated, $dsTargetUser, "selfRegularVisitRetrieve", "regularVisitRetrieve");

        $this->authentication->check($dsAuthenticated);
        $this->privilegesManagement->checkBool($dsAuthenticated, $privilege);

        return $db->getVisitsByOrder($dsTargetUser, $dsRegularOrder, $sortByTimestamp);
    }

    public function getVisitsByTimestamp(DSUser $dsAuthenticated, string $operator, int $timestamp, string $sortByTimestamp, IDataBaseRetrieveRegularVisits $db): DSRegularVisits
    {
        if (
            !in_array($sortByTimestamp, ['desc', 'asc']) ||
            !in_array($operator, ['<>', '=', '<=', '<', '>=', '>'])
        ) {
            throw new \InvalidArgumentException(
                '$sortByTimestamp variable must be one of the \'desc\' or \'asc\' values, \'' . $sortByTimestamp . '\' given.',
                500
            );
        }

        $privilege = "regularVisitRetrieve";

        $this->authentication->check($dsAuthenticated);
        $this->privilegesManagement->checkBool($dsAuthenticated, $privilege);

        return $db->getVisitsByTimestamp($operator, $timestamp, $sortByTimestamp);
    }
}
