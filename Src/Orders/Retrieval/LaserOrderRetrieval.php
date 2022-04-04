<?php

namespace TheClinicUseCases\Orders\Retrieval;

use TheClinicDataStructures\DataStructures\Order\Laser\DSLaserOrder;
use TheClinicDataStructures\DataStructures\Order\Laser\DSLaserOrders;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveLaserOrders;
use TheClinicUseCases\Traits\TraitGetPrivilegeFromInput;
use TheClinicUseCases\Privileges\PrivilegesManagement;

class LaserOrderRetrieval
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

    public function getLaserOrdersByPriceByUser(string $operator, int $price, DSUser $targetUser, DSUser $user, IDataBaseRetrieveLaserOrders $db): DSLaserOrders
    {
        if (!in_array($operator, self::$operatorValues)) {
            throw new \RuntimeException("The operator parameter has an invalid value.", 500);
        }

        $privilege = $this->getPrivilegeFromInput($user, $targetUser, "selfLaserOrdersRead", "laserOrdersRead");

        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, $privilege);

        return $db->getLaserOrdersByPriceByUser($operator, $price, $targetUser);
    }

    public function getLaserOrdersByPrice(int $lastOrderId = null, int $count, string $operator, int $price, DSUser $user, IDataBaseRetrieveLaserOrders $db): DSLaserOrders
    {
        if (!in_array($operator, self::$operatorValues)) {
            throw new \RuntimeException("The operator parameter has an invalid value.", 500);
        }

        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, "laserOrdersRead");

        return $db->getLaserOrdersByPrice($lastOrderId, $count, $operator, $price);
    }

    public function getLaserOrdersByTimeConsumptionByUser(string $operator, int $timeConsumption, DSUser $targetUser, DSUser $user, IDataBaseRetrieveLaserOrders $db): DSLaserOrders
    {
        if (!in_array($operator, self::$operatorValues)) {
            throw new \RuntimeException("The operator parameter has an invalid value.", 500);
        }

        $privilege = $this->getPrivilegeFromInput($user, $targetUser, "selfLaserOrdersRead", "laserOrdersRead");

        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, $privilege);

        return $db->getLaserOrdersByTimeConsumptionByUser($operator, $timeConsumption, $targetUser);
    }

    public function getLaserOrdersByTimeConsumption(int $lastOrderId = null, int $count, string $operator, int $timeConsumption, DSUser $user, IDataBaseRetrieveLaserOrders $db): DSLaserOrders
    {
        if (!in_array($operator, self::$operatorValues)) {
            throw new \RuntimeException("The operator parameter has an invalid value.", 500);
        }

        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, "laserOrdersRead");

        return $db->getLaserOrdersByTimeConsumption($count, $operator, $timeConsumption, $lastOrderId);
    }

    public function getLaserOrdersByUser(DSUser $targetUser, DSUser $user, IDataBaseRetrieveLaserOrders $db): DSLaserOrders
    {
        $privilege = $this->getPrivilegeFromInput($user, $targetUser, "selfLaserOrdersRead", "laserOrdersRead");

        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, $privilege);

        return $db->getLaserOrdersByUser($targetUser);
    }

    public function getLaserOrders(int $lastOrderId = null, int $count, DSUser $user, IDataBaseRetrieveLaserOrders $db): DSLaserOrders
    {
        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, "laserOrdersRead");

        return $db->getLaserOrders($count, $lastOrderId);
    }
}
