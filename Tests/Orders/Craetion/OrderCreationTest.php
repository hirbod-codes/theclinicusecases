<?php

namespace Tests\Orders\Craetion;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use Tests\TestCase;
use TheClinicDataStructures\DataStructures\Order\DSPackages;
use TheClinicDataStructures\DataStructures\Order\DSParts;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinic\Order\ICalculateLaserOrder;
use TheClinic\Order\ICalculateRegularOrder;
use TheClinic\Order\Laser\ILaserPriceCalculator;
use TheClinic\Order\Laser\ILaserTimeConsumptionCalculator;
use TheClinicDataStructures\DataStructures\Order\Laser\DSLaserOrder;
use TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrder;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Orders\Creation\OrderCreation;
use TheClinicUseCases\Orders\Interfaces\IDataBaseCreateLaserOrder;
use TheClinicUseCases\Orders\Interfaces\IDataBaseCreateRegularOrder;
use TheClinicUseCases\Orders\OrderManagement;
use TheClinicUseCases\Privileges\PrivilegesManagement;

class OrderCreationTest extends TestCase
{
    private Generator $faker;

    private DSUser|\Mockery\MockInterface $user;

    private Authentication|\Mockery\MockInterface $authentication;

    private ICalculateRegularOrder|\Mockery\MockInterface $iCalculateRegularOrder;

    private ICalculateLaserOrder|\Mockery\MockInterface $iCalculateLaserOrder;

    protected function setUp(): void
    {
        parent::setUp();

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

    public function testCreateRegularOrder(): void
    {
        $this->testCreateRegularOrderWithIds(14, 14, "selfRegularOrderCreate");
        $this->testCreateRegularOrderWithIds(14, 15, "regularOrderCreate");
    }

    private function testCreateRegularOrderWithIds(int $userId, int $targetUserId, string $privilege): void
    {
        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, $privilege);

        $this->user->shouldReceive("getId")->andReturn($userId);

        /** @var \TheClinicDataStructures\DataStructures\User\DSUser|\Mockery\MockInterface $targetUser */
        $targetUser = $this->makeUser();
        $targetUser->shouldReceive("getId")->andReturn($targetUserId);

        $price = 400000;
        $timeConsumption = 600;

        $this->iCalculateRegularOrder->shouldReceive("calculatePrice")->andReturn($price);
        $this->iCalculateRegularOrder->shouldReceive("calculateTimeConsumption")->andReturn($timeConsumption);

        /** @var \TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrder|\Mockery\MockInterface $dsOrder */
        $dsOrder = Mockery::mock(DSRegularOrder::class);
        /** @var \TheClinicUseCases\Orders\Interfaces\IDataBaseCreateRegularOrder|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseCreateRegularOrder::class);
        $db->shouldReceive("createRegularOrder")->with($targetUser, $price, $timeConsumption)->andReturn($dsOrder);

        $order = (new OrderCreation(
            $this->authentication,
            $privilegesManagement,
            $this->iCalculateRegularOrder,
            $this->iCalculateLaserOrder,
            $this->iLaserPriceCalculator,
            $this->iLaserTimeConsumptionCalculator
        ))
            ->createRegularOrder($targetUser, $this->user, $db);
        $this->assertInstanceOf(DSRegularOrder::class, $order);
    }

    public function testCreateLaserOrder(): void
    {
        $this->markTestIncomplete();
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
        /** @var \TheClinicDataStructures\DataStructures\Order\Regular\DSLaserOrder|\Mockery\MockInterface $dsLaserOrder */
        $dsLaserOrder = Mockery::mock(DSLaserOrder::class);

        /** @var \TheClinicUseCases\Orders\Interfaces\IDataBaseCreateLaserOrder|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseCreateLaserOrder::class);
        $db->shouldReceive("createLaserOrder")->with($targetUser, $dsParts, $dsPackages, $price, $timeConsumption, $priceWithoutDiscount)->andReturn($dsLaserOrder);

        $this->iCalculateLaserOrder->shouldReceive("calculatePrice")->with($dsParts, $dsPackages, $this->iLaserPriceCalculator)->andReturn($price);
        $this->iCalculateLaserOrder->shouldReceive("calculateTimeConsumption")->with($dsParts, $dsPackages, $this->iLaserTimeConsumptionCalculator)->andReturn($timeConsumption);
        $this->iCalculateLaserOrder->shouldReceive("calculatePriceWithoutDiscount")->with($dsParts, $dsPackages, $this->iLaserPriceCalculator)->andReturn($priceWithoutDiscount);

        $order = (new OrderManagement($this->authentication, $privilegesManagement, $this->iCalculateRegularOrder, $this->iCalculateLaserOrder, $this->iLaserPriceCalculator, $this->iLaserTimeConsumptionCalculator))
            ->createLaserOrder($this->user, $targetUser, $dsParts, $dsPackages, $db);
        $this->assertInstanceOf(DSLaserOrder::class, $order);
    }
}
