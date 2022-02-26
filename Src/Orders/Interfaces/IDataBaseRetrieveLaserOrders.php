<?php

namespace TheClinicUseCases\Orders\Interfaces;

use TheClinicDataStructures\DataStructures\Order\Laser\DSLaserOrder;
use TheClinicDataStructures\DataStructures\Order\Laser\DSLaserOrders;
use TheClinicDataStructures\DataStructures\User\DSUser;

interface IDataBaseRetrieveLaserOrders
{
    /**
     * @param string $operator Must be one the followings: "<=" ">=" "=" "<>" "<" ">"
     * @param integer $price
     * @param \TheClinicDataStructures\DataStructures\User\DSUser $targetUser
     * @return \TheClinicDataStructures\DataStructures\Order\Laser\DSLaserOrders
     */
    public function getLaserOrdersByPriceByUser(string $operator, int $price, DSUser $targetUser): DSLaserOrders;

    /**
     * @param string $operator Must be one the followings: "<=" ">=" "=" "<>" "<" ">"
     * @param integer $price
     * @param \TheClinicDataStructures\DataStructures\User\DSUser $targetUser
     * @return \TheClinicDataStructures\DataStructures\Order\Laser\DSLaserOrders
     */
    public function getLaserOrdersByPrice(int $lastOrderId = null, int $count, string $operator, int $price): DSLaserOrders;

    /**
     * @param string $operator Must be one the followings: "<=" ">=" "=" "<>" "<" ">"
     * @param integer $price
     * @param \TheClinicDataStructures\DataStructures\User\DSUser $targetUser
     * @return \TheClinicDataStructures\DataStructures\Order\Laser\DSLaserOrders
     */
    public function getLaserOrdersByTimeConsumptionByUser(string $operator, int $timeCosumption, DSUser $targetUser): DSLaserOrders;

    /**
     * @param string $operator Must be one the followings: "<=" ">=" "=" "<>" "<" ">"
     * @param integer $price
     * @param \TheClinicDataStructures\DataStructures\User\DSUser $targetUser
     * @return \TheClinicDataStructures\DataStructures\Order\Laser\DSLaserOrders
     */
    public function getLaserOrdersByTimeConsumption(int $lastOrderId = null, int $count, string $operator, int $timeCosumption): DSLaserOrders;

    public function getLaserOrdersByUser(DSUser $targetUser): DSLaserOrders;

    public function getLaserOrders(int $lastOrderId = null, int $count): DSLaserOrders;

    public function getLaserOrderById(int $id): DSLaserOrder;
}
