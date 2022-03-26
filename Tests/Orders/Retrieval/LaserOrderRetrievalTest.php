<?php

namespace Tests\Orders\Retrieval;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use Tests\TestCase;
use TheClinicDataStructures\DataStructures\Order\Laser\DSLaserOrder;
use TheClinicDataStructures\DataStructures\Order\Laser\DSLaserOrders;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveLaserOrders;
use TheClinicUseCases\Orders\Retrieval\LaserOrderRetrieval;
use TheClinicUseCases\Privileges\PrivilegesManagement;

class LaserOrderRetrievalTest extends TestCase
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

    public function testGetLaserOrdersByPriceByUser(): void
    {
        $this->getLaserOrdersByPriceByUserTester(14, 14, "selfLaserOrdersRead");
        $this->getLaserOrdersByPriceByUserTester(14, 15, "laserOrdersRead");
    }

    private function getLaserOrdersByPriceByUserTester(int $userId, int $targetUserId, string $privilege): void
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

        /** @var \TheClinic\DataStructures\Order\Laser\DSLaserOrders|\Mockery\MockInterface $dsLaserOrders */
        $dsLaserOrders = Mockery::mock(DSLaserOrders::class);

        /** @var \TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveLaserOrders|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseRetrieveLaserOrders::class);
        $db->shouldReceive("getLaserOrdersByPriceByUser")->with($operator, $price, $targetUser)->andReturn($dsLaserOrders);

        $dsLaserOrders = (new LaserOrderRetrieval($this->authentication, $privilegesManagement))
            ->getLaserOrdersByPriceByUser($operator, $price, $targetUser, $this->user, $db);
        $this->assertInstanceOf(DSLaserOrders::class, $dsLaserOrders);
    }

    public function testGetLaserOrdersByPrice(): void
    {
        $operator = $this->faker->randomElement($this->operatorValues);
        $price = $this->faker->numberBetween(100000, 500000);
        $lastOrderId = $this->faker->numberBetween(1, 1000);
        $count = $this->faker->numberBetween(1, 30);

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, "laserOrdersRead");

        /** @var \TheClinic\DataStructures\Order\Laser\DSLaserOrders|\Mockery\MockInterface $dsLaserOrders */
        $dsLaserOrders = Mockery::mock(DSLaserOrders::class);
        /** @var \TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveLaserOrders|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseRetrieveLaserOrders::class);
        $db->shouldReceive("getLaserOrdersByPrice")->with($lastOrderId, $count, $operator, $price)->andReturn($dsLaserOrders);

        $dsLaserOrders = (new LaserOrderRetrieval($this->authentication, $privilegesManagement))
            ->getLaserOrdersByPrice($lastOrderId, $count, $operator, $price, $this->user, $db);
        $this->assertInstanceOf(DSLaserOrders::class, $dsLaserOrders);
    }

    public function testGetLaserOrdersByTimeConsumptionByUser(): void
    {
        $this->getLaserOrdersByTimeConsumptionByUserTester(14, 14, "selfLaserOrdersRead");
        $this->getLaserOrdersByTimeConsumptionByUserTester(14, 15, "laserOrdersRead");
    }

    private function getLaserOrdersByTimeConsumptionByUserTester(int $userId, int $targetUserId, string $privilege): void
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

        /** @var \TheClinic\DataStructures\Order\Laser\DSLaserOrders|\Mockery\MockInterface $dsLaserOrders */
        $dsLaserOrders = Mockery::mock(DSLaserOrders::class);

        /** @var \TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveLaserOrders|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseRetrieveLaserOrders::class);
        $db->shouldReceive("getLaserOrdersByTimeConsumptionByUser")->with($operator, $timeConsumption, $targetUser)->andReturn($dsLaserOrders);

        $dsLaserOrders = (new LaserOrderRetrieval($this->authentication, $privilegesManagement))
            ->getLaserOrdersByTimeConsumptionByUser($operator, $timeConsumption, $targetUser, $this->user, $db);
        $this->assertInstanceOf(DSLaserOrders::class, $dsLaserOrders);
    }

    public function testGetLaserOrdersByTimeConsumption(): void
    {
        $operator = $this->faker->randomElement($this->operatorValues);
        $price = $this->faker->numberBetween(100000, 500000);
        $lastOrderId = $this->faker->numberBetween(1, 1000);
        $count = $this->faker->numberBetween(1, 30);

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, "laserOrdersRead");

        /** @var \TheClinic\DataStructures\Order\Laser\DSLaserOrders|\Mockery\MockInterface $dsLaserOrders */
        $dsLaserOrders = Mockery::mock(DSLaserOrders::class);
        /** @var \TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveLaserOrders|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseRetrieveLaserOrders::class);
        $db->shouldReceive("getLaserOrdersByTimeConsumption")->with($count, $operator, $price, $lastOrderId)->andReturn($dsLaserOrders);

        $dsLaserOrders = (new LaserOrderRetrieval($this->authentication, $privilegesManagement))
            ->getLaserOrdersByTimeConsumption($lastOrderId, $count, $operator, $price, $this->user, $db);
        $this->assertInstanceOf(DSLaserOrders::class, $dsLaserOrders);
    }

    public function testGetLaserOrdersByUser(): void
    {
        $this->getLaserOrdersByUserTester(14, 14, "selfLaserOrdersRead");
        $this->getLaserOrdersByUserTester(14, 15, "laserOrdersRead");
    }

    private function getLaserOrdersByUserTester(int $userId, int $targetUserId, string $privilege): void
    {
        $this->user->shouldReceive("getId")->andReturn($userId);

        /** @var \TheClinicDataStructures\DataStructures\User\DSUser|\Mockery\MockInterface $targetUser */
        $targetUser = $this->makeUser();
        $targetUser->shouldReceive("getId")->andReturn($targetUserId);

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, $privilege);

        /** @var \TheClinic\DataStructures\Order\Laser\DSLaserOrders|\Mockery\MockInterface $dsLaserOrders */
        $dsLaserOrders = Mockery::mock(DSLaserOrders::class);

        /** @var \TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveLaserOrders|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseRetrieveLaserOrders::class);
        $db->shouldReceive("getLaserOrdersByUser")->with($targetUser)->andReturn($dsLaserOrders);

        $dsLaserOrders = (new LaserOrderRetrieval($this->authentication, $privilegesManagement))
            ->getLaserOrdersByUser($targetUser, $this->user, $db);
        $this->assertInstanceOf(DSLaserOrders::class, $dsLaserOrders);
    }

    public function testGetLaserOrders(): void
    {
        $lastOrderId = $this->faker->numberBetween(1, 1000);
        $count = $this->faker->numberBetween(1, 30);

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, "laserOrdersRead");

        /** @var \TheClinic\DataStructures\Order\Laser\DSLaserOrders|\Mockery\MockInterface $dsLaserOrders */
        $dsLaserOrders = Mockery::mock(DSLaserOrders::class);
        /** @var \TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveLaserOrders|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseRetrieveLaserOrders::class);
        $db->shouldReceive("getLaserOrders")->with($count, $lastOrderId)->andReturn($dsLaserOrders);

        $dsLaserOrders = (new LaserOrderRetrieval($this->authentication, $privilegesManagement))
            ->getLaserOrders($lastOrderId, $count, $this->user, $db);
        $this->assertInstanceOf(DSLaserOrders::class, $dsLaserOrders);
    }

    public function testGetLaserOrderById(): void
    {
        $id = $this->faker->numberBetween(1, 1000);

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, "laserOrdersRead");

        /** @var \TheClinic\DataStructures\Order\Laser\DSLaserOrder|\Mockery\MockInterface $dsLaserOrder */
        $dsLaserOrder = Mockery::mock(DSLaserOrder::class);
        /** @var \TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveLaserOrders|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseRetrieveLaserOrders::class);
        $db->shouldReceive("getLaserOrderById")->with($id)->andReturn($dsLaserOrder);

        $dsLaserOrders = (new LaserOrderRetrieval($this->authentication, $privilegesManagement))
            ->getLaserOrderById($id, $this->user, $db);
        $this->assertInstanceOf(DSLaserOrder::class, $dsLaserOrders);
    }
}
