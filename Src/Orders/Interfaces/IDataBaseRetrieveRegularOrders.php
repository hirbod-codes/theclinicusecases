<?php

namespace TheClinicUseCases\Orders\Interfaces;

use TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrder;
use TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrders;
use TheClinicDataStructures\DataStructures\User\DSUser;

interface IDataBaseRetrieveRegularOrders
{
    /**
     * @param string $operator Must be one the followings: "<=" ">=" "=" "<>" "<" ">"
     * @param integer $price
     * @param DSUser $targetUser
     * @return DSRegularOrders
     */
    public function getRegularOrdersByPriceByUser(string $operator, int $price, DSUser $targetUser): DSRegularOrders;

    /**
     * @param string $operator Must be one the followings: "<=" ">=" "=" "<>" "<" ">"
     * @param integer $price
     * @param DSUser $targetUser
     * @return DSRegularOrders
     */
    public function getRegularOrdersByPrice(int $lastOrderId = null, int $count, string $operator, int $price): DSRegularOrders;

    /**
     * @param string $operator Must be one the followings: "<=" ">=" "=" "<>" "<" ">"
     * @param integer $price
     * @param DSUser $targetUser
     * @return DSRegularOrders
     */
    public function getRegularOrdersByTimeConsumptionByUser(string $operator, int $timeCosumption, DSUser $targetUser): DSRegularOrders;

    /**
     * @param string $operator Must be one the followings: "<=" ">=" "=" "<>" "<" ">"
     * @param integer $price
     * @param DSUser $targetUser
     * @return DSRegularOrders
     */
    public function getRegularOrdersByTimeConsumption(int $count, string $operator, int $timeCosumption, int $lastOrderId = null): DSRegularOrders;

    public function getRegularOrdersByUser(DSUser $targetUser): DSRegularOrders;

    public function getRegularOrders(int $count, int $lastOrderId = null): DSRegularOrders;
}
