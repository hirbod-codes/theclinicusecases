<?php

namespace Tests\Accounts;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use Tests\TestCase;
use TheClinicDataStructures\DataStructures\Order\DSLaserOrder;
use TheClinicDataStructures\DataStructures\Order\DSLaserOrders;
use TheClinicDataStructures\DataStructures\Order\DSOrder;
use TheClinicDataStructures\DataStructures\Order\DSOrders;
use TheClinicDataStructures\DataStructures\Order\DSPackages;
use TheClinicDataStructures\DataStructures\Order\DSParts;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinic\Order\ICalculateLaserOrder;
use TheClinic\Order\ICalculateRegularOrder;
use TheClinic\Order\Laser\ILaserPriceCalculator;
use TheClinic\Order\Laser\ILaserTimeConsumptionCalculator;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Orders\Interfaces\IDataBaseCreateLaserOrder;
use TheClinicUseCases\Orders\Interfaces\IDataBaseCreateRegularOrder;
use TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveLaserOrders;
use TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveOrders;
use TheClinicUseCases\Orders\OrderManagement;
use TheClinicUseCases\Privileges\PrivilegesManagement;

class OrderManagementTest extends TestCase
{
    private Generator $faker;

    private DSUser|\Mockery\MockInterface $user;

    private Authentication|\Mockery\MockInterface $authentication;

    private ICalculateRegularOrder|\Mockery\MockInterface $iCalculateRegularOrder;

    private ICalculateLaserOrder|\Mockery\MockInterface $iCalculateLaserOrder;

    protected function setUp(): void
    {
        $this->faker = Factory::create();

        $this->user = $this->makeUser();

        /** @var \TheClinicUseCases\Accounts\Authentication|\Mockery\MockInterface $authentication */
        $this->authentication = Mockery::mock(Authentication::class);
        $this->authentication->shouldReceive("check")->with($this->user);

        /** @var \TheClinic\Order\ICalculateRegularOrder|\Mockery\MockInterface $iCalculateRegularOrder */
        $this->iCalculateRegularOrder = Mockery::mock(ICalculateRegularOrder::class);

        /** @var \TheClinic\Order\ICalculateLaserOrder|\Mockery\MockInterface $iCalculateLaserOrder */
        $this->iCalculateLaserOrder = Mockery::mock(ICalculateLaserOrder::class);

        /** @var \TheClinic\Order\Laser\ILaserPriceCalculator|\Mockery\MockInterface $iLaserPriceCalculator */
        $this->iLaserPriceCalculator = Mockery::mock(ILaserPriceCalculator::class);
        /** @var \TheClinic\Order\Laser\ILaserTimeConsumptionCalculator|\Mockery\MockInterface $iLaserTimeConsumptionCalculator */
        $this->iLaserTimeConsumptionCalculator = Mockery::mock(ILaserTimeConsumptionCalculator::class);
    }

    private function makeUser(): DSUser
    {
        /** @var \TheClinicDataStructures\DataStructures\User\DSUser|\Mockery\MockInterface $user */
        $user = Mockery::mock(DSUser::class);
        return $user;
    }

    public function testGetRegularOrders(): void
    {
        $lastOrderId = $this->faker->numberBetween(1, 1000);
        $count = $this->faker->numberBetween(1, 30);

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, "regularOrdersRead");

        /** @var \TheClinic\DataStructures\Order\DSOrders|\Mockery\MockInterface $dsOrders */
        $dsOrders = Mockery::mock(DSOrders::class);

        /** @var \TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveOrders|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseRetrieveOrders::class);
        $db->shouldReceive("getRegularOrders")->with($lastOrderId, $count)->andReturn($dsOrders);

        $orders = (new OrderManagement($this->authentication, $privilegesManagement, $this->iCalculateRegularOrder, $this->iCalculateLaserOrder, $this->iLaserPriceCalculator, $this->iLaserTimeConsumptionCalculator))
            ->getRegularOrders($this->user, $lastOrderId, $count, $db);
        $this->assertInstanceOf(DSOrders::class, $orders);
    }

    public function testGetLaserOrders(): void
    {
        $lastOrderId = $this->faker->numberBetween(1, 1000);
        $count = $this->faker->numberBetween(1, 30);

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, "laserOrdersRead");

        /** @var \TheClinic\DataStructures\Order\DSLaserOrders|\Mockery\MockInterface $dsLaserOrders */
        $dsLaserOrders = Mockery::mock(DSLaserOrders::class);

        /** @var \TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveLaserOrders|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseRetrieveLaserOrders::class);
        $db->shouldReceive("getLaserOrders")->with($lastOrderId, $count)->andReturn($dsLaserOrders);

        $orders = (new OrderManagement($this->authentication, $privilegesManagement, $this->iCalculateRegularOrder, $this->iCalculateLaserOrder, $this->iLaserPriceCalculator, $this->iLaserTimeConsumptionCalculator))
            ->getLaserOrders($this->user, $lastOrderId, $count, $db);
        $this->assertInstanceOf(DSLaserOrders::class, $orders);
    }

    public function testCreateOrder(): void
    {
        $price = 400000;
        $timeConsumption = 600;

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, "regularOrderCreate");

        $this->iCalculateRegularOrder->shouldReceive("calculatePrice")->andReturn($price);
        $this->iCalculateRegularOrder->shouldReceive("calculateTimeConsumption")->andReturn($timeConsumption);

        /** @var \TheClinicDataStructures\DataStructures\Order\DSOrder|\Mockery\MockInterface $dsOrder */
        $dsOrder = Mockery::mock(DSOrder::class);

        /** @var \TheClinicUseCases\Orders\Interfaces\IDataBaseCreateRegularOrder|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseCreateRegularOrder::class);
        $db->shouldReceive("createRegularOrder")->with($this->user, $price, $timeConsumption)->andReturn($dsOrder);

        $order = (new OrderManagement($this->authentication, $privilegesManagement, $this->iCalculateRegularOrder, $this->iCalculateLaserOrder, $this->iLaserPriceCalculator, $this->iLaserTimeConsumptionCalculator))
            ->createRegularOrder($this->user, $db);
        $this->assertInstanceOf(DSOrder::class, $order);
    }

    public function testCreateLaserOrder(): void
    {
        $gender = "Male";
        $price = 400000;
        $timeConsumption = 600;
        $priceWithoutDiscount = 600000;

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, "laserOrderCreate");

        /** @var \TheClinicDataStructures\DataStructures\User\DSUser|\Mockery\MockInterface $targetUser */
        $targetUser = $this->makeUser();
        $targetUser->shouldReceive("getGender")->twice()->andReturn($gender);

        /** @var \TheClinicDataStructures\DataStructures\Order\DSParts|\Mockery\MockInterface $dsParts */
        $dsParts = Mockery::mock(DSParts::class);
        $dsParts->shouldReceive("getGender")->once()->andReturn($gender);
        /** @var \TheClinicDataStructures\DataStructures\Order\DSPackages|\Mockery\MockInterface $dsPackages */
        $dsPackages = Mockery::mock(DSPackages::class);
        $dsPackages->shouldReceive("getGender")->once()->andReturn($gender);
        /** @var \TheClinicDataStructures\DataStructures\Order\DSLaserOrder|\Mockery\MockInterface $dsLaserOrder */
        $dsLaserOrder = Mockery::mock(DSLaserOrder::class);

        /** @var \TheClinicUseCases\Orders\Interfaces\IDataBaseCreateLaserOrder|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseCreateLaserOrder::class);
        $db->shouldReceive("createLaserOrder")->with($targetUser, $dsParts, $dsPackages, $price, $timeConsumption, $priceWithoutDiscount)->andReturn($dsLaserOrder);

        $this->iCalculateLaserOrder->shouldReceive("calculatePrice")->with($dsParts, $dsPackages, $this->iLaserPriceCalculator)->andReturn($price);
        $this->iCalculateLaserOrder->shouldReceive("calculateTimeConsumption")->with($dsParts, $dsPackages, $this->iLaserTimeConsumptionCalculator)->andReturn($timeConsumption);
        $this->iCalculateLaserOrder->shouldReceive("calculatePriceWithoutDiscount")->with($dsParts, $dsPackages, $this->iLaserPriceCalculator)->andReturn($priceWithoutDiscount);

        $order = (new OrderManagement($this->authentication, $privilegesManagement, $this->iCalculateRegularOrder, $this->iCalculateLaserOrder, $this->iLaserPriceCalculator, $this->iLaserTimeConsumptionCalculator))
            ->createLaserOrder($this->user, $targetUser, $dsParts, $dsPackages, $db);
        $this->assertInstanceOf(DSOrder::class, $order);
    }
}
