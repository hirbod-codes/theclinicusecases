<?php

namespace Tests\Orders\Retrieval;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use Tests\TestCase;
use TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrder;
use TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrders;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveRegularOrders;
use TheClinicUseCases\Orders\Retrieval\RegularOrderRetrieval;
use TheClinicUseCases\Privileges\PrivilegesManagement;

class RegularOrderRetrievalTest extends TestCase
{
    private Generator $faker;

    private array $operatorValues = ["<=", ">=", "=", "<>", "<", ">"];

    private DSUser|\Mockery\MockInterface $user;

    private Authentication|\Mockery\MockInterface $authentication;

    protected function setUp(): void
    {
        $this->faker = Factory::create();

        $this->user = $this->makeUser();

        /** @var \TheClinicUseCases\Accounts\Authentication|\Mockery\MockInterface $authentication */
        $this->authentication = Mockery::mock(Authentication::class);
        $this->authentication->shouldReceive("check")->with($this->user);
    }

    private function makeUser(): DSUser
    {
        /** @var \TheClinicDataStructures\DataStructures\User\DSUser|\Mockery\MockInterface $user */
        $user = Mockery::mock(DSUser::class);
        return $user;
    }

    public function testGetRegularOrdersByPriceByUser(): void
    {
        $this->getRegularOrdersByPriceByUserTester(14, 14, "selfRegularOrdersRead");
        $this->getRegularOrdersByPriceByUserTester(14, 15, "regularOrdersRead");
    }

    private function getRegularOrdersByPriceByUserTester(int $userId, int $targetUserId, string $privilege): void
    {
        $operator = $this->faker->randomElement($this->operatorValues);
        $price = $this->faker->numberBetween(100000, 500000);

        $this->user->shouldReceive("getId")->andReturn($userId);

        /** @var \TheClinicDataStructures\DataStructures\User\DSUser|\Mockery\MockInterface $targetUser */
        $targetUser = $this->makeUser();
        $targetUser->shouldReceive("getId")->andReturn($targetUserId);

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, $privilege);

        /** @var \TheClinic\DataStructures\Order\Regular\DSRegularOrders|\Mockery\MockInterface $dsRegularOrders */
        $dsRegularOrders = Mockery::mock(DSRegularOrders::class);

        /** @var \TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveRegularOrders|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseRetrieveRegularOrders::class);
        $db->shouldReceive("getRegularOrdersByPriceByUser")->with($operator, $price, $targetUser)->andReturn($dsRegularOrders);

        $dsRegularOrders = (new RegularOrderRetrieval($this->authentication, $privilegesManagement))
            ->getRegularOrdersByPriceByUser($operator, $price, $targetUser, $this->user, $db);
        $this->assertInstanceOf(DSRegularOrders::class, $dsRegularOrders);
    }

    public function testGetRegularOrdersByPrice(): void
    {
        $operator = $this->faker->randomElement($this->operatorValues);
        $price = $this->faker->numberBetween(100000, 500000);
        $lastOrderId = $this->faker->numberBetween(1, 1000);
        $count = $this->faker->numberBetween(1, 30);

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, "regularOrdersRead");

        /** @var \TheClinic\DataStructures\Order\Regular\DSRegularOrders|\Mockery\MockInterface $dsRegularOrders */
        $dsRegularOrders = Mockery::mock(DSRegularOrders::class);
        /** @var \TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveRegularOrders|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseRetrieveRegularOrders::class);
        $db->shouldReceive("getRegularOrdersByPrice")->with($lastOrderId, $count, $operator, $price)->andReturn($dsRegularOrders);

        $dsRegularOrders = (new RegularOrderRetrieval($this->authentication, $privilegesManagement))
            ->getRegularOrdersByPrice($lastOrderId, $count, $operator, $price, $this->user, $db);
        $this->assertInstanceOf(DSRegularOrders::class, $dsRegularOrders);
    }

    public function testGetRegularOrdersByTimeConsumptionByUser(): void
    {
        $this->getRegularOrdersByTimeConsumptionByUserTester(14, 14, "selfRegularOrdersRead");
        $this->getRegularOrdersByTimeConsumptionByUserTester(14, 15, "regularOrdersRead");
    }

    private function getRegularOrdersByTimeConsumptionByUserTester(int $userId, int $targetUserId, string $privilege): void
    {
        $operator = $this->faker->randomElement($this->operatorValues);
        $timeConsumption = $this->faker->numberBetween(600, 3600);

        $this->user->shouldReceive("getId")->andReturn($userId);

        /** @var \TheClinicDataStructures\DataStructures\User\DSUser|\Mockery\MockInterface $targetUser */
        $targetUser = $this->makeUser();
        $targetUser->shouldReceive("getId")->andReturn($targetUserId);

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, $privilege);

        /** @var \TheClinic\DataStructures\Order\Regular\DSRegularOrders|\Mockery\MockInterface $dsRegularOrders */
        $dsRegularOrders = Mockery::mock(DSRegularOrders::class);

        /** @var \TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveRegularOrders|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseRetrieveRegularOrders::class);
        $db->shouldReceive("getRegularOrdersByTimeConsumptionByUser")->with($operator, $timeConsumption, $targetUser)->andReturn($dsRegularOrders);

        $dsRegularOrders = (new RegularOrderRetrieval($this->authentication, $privilegesManagement))
            ->getRegularOrdersByTimeConsumptionByUser($operator, $timeConsumption, $targetUser, $this->user, $db);
        $this->assertInstanceOf(DSRegularOrders::class, $dsRegularOrders);
    }

    public function testGetRegularOrdersByTimeConsumption(): void
    {
        $operator = $this->faker->randomElement($this->operatorValues);
        $price = $this->faker->numberBetween(100000, 500000);
        $lastOrderId = $this->faker->numberBetween(1, 1000);
        $count = $this->faker->numberBetween(1, 30);

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, "regularOrdersRead");

        /** @var \TheClinic\DataStructures\Order\Regular\DSRegularOrders|\Mockery\MockInterface $dsRegularOrders */
        $dsRegularOrders = Mockery::mock(DSRegularOrders::class);
        /** @var \TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveRegularOrders|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseRetrieveRegularOrders::class);
        $db->shouldReceive("getRegularOrdersByTimeConsumption")->with($lastOrderId, $count, $operator, $price)->andReturn($dsRegularOrders);

        $dsRegularOrders = (new RegularOrderRetrieval($this->authentication, $privilegesManagement))
            ->getRegularOrdersByTimeConsumption($lastOrderId, $count, $operator, $price, $this->user, $db);
        $this->assertInstanceOf(DSRegularOrders::class, $dsRegularOrders);
    }

    public function testGetRegularOrdersByUser(): void
    {
        $this->getRegularOrdersByUserTester(14, 14, "selfRegularOrdersRead");
        $this->getRegularOrdersByUserTester(14, 15, "regularOrdersRead");
    }

    private function getRegularOrdersByUserTester(int $userId, int $targetUserId, string $privilege): void
    {
        $this->user->shouldReceive("getId")->andReturn($userId);

        /** @var \TheClinicDataStructures\DataStructures\User\DSUser|\Mockery\MockInterface $targetUser */
        $targetUser = $this->makeUser();
        $targetUser->shouldReceive("getId")->andReturn($targetUserId);

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, $privilege);

        /** @var \TheClinic\DataStructures\Order\Regular\DSRegularOrders|\Mockery\MockInterface $dsRegularOrders */
        $dsRegularOrders = Mockery::mock(DSRegularOrders::class);

        /** @var \TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveRegularOrders|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseRetrieveRegularOrders::class);
        $db->shouldReceive("getRegularOrdersByUser")->with($targetUser)->andReturn($dsRegularOrders);

        $dsRegularOrders = (new RegularOrderRetrieval($this->authentication, $privilegesManagement))
            ->getRegularOrdersByUser($targetUser, $this->user, $db);
        $this->assertInstanceOf(DSRegularOrders::class, $dsRegularOrders);
    }

    public function testGetRegularOrders(): void
    {
        $lastOrderId = $this->faker->numberBetween(1, 1000);
        $count = $this->faker->numberBetween(1, 30);

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, "regularOrdersRead");

        /** @var \TheClinic\DataStructures\Order\Regular\DSRegularOrders|\Mockery\MockInterface $dsRegularOrders */
        $dsRegularOrders = Mockery::mock(DSRegularOrders::class);
        /** @var \TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveRegularOrders|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseRetrieveRegularOrders::class);
        $db->shouldReceive("getRegularOrders")->with($lastOrderId, $count)->andReturn($dsRegularOrders);

        $dsRegularOrders = (new RegularOrderRetrieval($this->authentication, $privilegesManagement))
            ->getRegularOrders($lastOrderId, $count, $this->user, $db);
        $this->assertInstanceOf(DSRegularOrders::class, $dsRegularOrders);
    }

    public function testGetRegularOrderById(): void
    {
        $id = $this->faker->numberBetween(1, 1000);

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, "regularOrdersRead");

        /** @var \TheClinic\DataStructures\Order\Regular\DSRegularOrder|\Mockery\MockInterface $dsRegularOrder */
        $dsRegularOrder = Mockery::mock(DSRegularOrder::class);
        /** @var \TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveRegularOrders|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseRetrieveRegularOrders::class);
        $db->shouldReceive("getRegularOrderById")->with($id)->andReturn($dsRegularOrder);

        $dsRegularOrders = (new RegularOrderRetrieval($this->authentication, $privilegesManagement))
            ->getRegularOrderById($id, $this->user, $db);
        $this->assertInstanceOf(DSRegularOrder::class, $dsRegularOrders);
    }
}
