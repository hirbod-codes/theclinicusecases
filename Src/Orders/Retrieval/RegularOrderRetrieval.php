<?php

namespace TheClinicUseCases\Orders\Retrieval;

use TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrder;
use TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrders;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveRegularOrders;
use TheClinicUseCases\Traits\TraitGetPrivilegeFromInput;
use TheClinicUseCases\Privileges\PrivilegesManagement;

class RegularOrderRetrieval
{
    use TraitGetPrivilegeFromInput;

    private static array $operatorValues = ["<=", ">=", "=", "<>", "<", ">"];

    private Authentication $authentication;

    private PrivilegesManagement $privilegesManagement;

    public function __construct(
        Authentication $authentication = null,
        PrivilegesManagement $privilegesManagement = null,
    ) {
        $this->authentication = $authentication ?: new Authentication;
        $this->privilegesManagement = $privilegesManagement ?: new PrivilegesManagement;
    }

    public function getRegularOrdersByPriceByUser(string $operator, int $price, DSUser $targetUser, DSUser $user, IDataBaseRetrieveRegularOrders $db): DSRegularOrders
    {
        if (!in_array($operator, self::$operatorValues)) {
            throw new \RuntimeException("The operator parameter has an invalid value.", 500);
        }

        $privilege = $this->getPrivilegeFromInput($user, $targetUser, "selfRegularOrdersRead", "regularOrdersRead");

        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, $privilege);

        return $db->getRegularOrdersByPriceByUser($operator, $price, $targetUser);
    }

    public function getRegularOrdersByPrice(int $lastOrderId = null, int $count, string $operator, int $price, DSUser $user, IDataBaseRetrieveRegularOrders $db): DSRegularOrders
    {
        if (!in_array($operator, self::$operatorValues)) {
            throw new \RuntimeException("The operator parameter has an invalid value.", 500);
        }

        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, "regularOrdersRead");

        return $db->getRegularOrdersByPrice($lastOrderId, $count, $operator, $price);
    }

    public function getRegularOrdersByTimeConsumptionByUser(string $operator, int $timeConsumption, DSUser $targetUser, DSUser $user, IDataBaseRetrieveRegularOrders $db): DSRegularOrders
    {
        if (!in_array($operator, self::$operatorValues)) {
            throw new \RuntimeException("The operator parameter has an invalid value.", 500);
        }

        $privilege = $this->getPrivilegeFromInput($user, $targetUser, "selfRegularOrdersRead", "regularOrdersRead");

        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, $privilege);

        return $db->getRegularOrdersByTimeConsumptionByUser($operator, $timeConsumption, $targetUser);
    }

    public function getRegularOrdersByTimeConsumption(int $lastOrderId = null, int $count, string $operator, int $timeConsumption, DSUser $user, IDataBaseRetrieveRegularOrders $db): DSRegularOrders
    {
        if (!in_array($operator, self::$operatorValues)) {
            throw new \RuntimeException("The operator parameter has an invalid value.", 500);
        }

        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, "regularOrdersRead");

        return $db->getRegularOrdersByTimeConsumption($count, $operator, $timeConsumption, $lastOrderId);
    }

    public function getRegularOrdersByUser(DSUser $targetUser, DSUser $user, IDataBaseRetrieveRegularOrders $db): DSRegularOrders
    {
        $privilege = $this->getPrivilegeFromInput($user, $targetUser, "selfRegularOrdersRead", "regularOrdersRead");

        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, $privilege);

        return $db->getRegularOrdersByUser($targetUser);
    }

    public function getRegularOrders(int $lastOrderId = null, int $count, DSUser $user, IDataBaseRetrieveRegularOrders $db): DSRegularOrders
    {
        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, "regularOrdersRead");

        return $db->getRegularOrders($count, $lastOrderId);
    }
}
