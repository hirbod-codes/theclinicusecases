<?php

namespace Tests\Orders\Craetion;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use Tests\TestCase;
use TheClinic\Exceptions\Order\InvalidGenderException;
use TheClinic\Exceptions\Order\NoPackageOrPartException;
use TheClinicDataStructures\DataStructures\Order\DSPackages;
use TheClinicDataStructures\DataStructures\Order\DSParts;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinic\Order\ICalculateLaserOrder;
use TheClinic\Order\Laser\ILaserPriceCalculator;
use TheClinic\Order\Laser\ILaserTimeConsumptionCalculator;
use TheClinicDataStructures\DataStructures\Order\Laser\DSLaserOrder;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Orders\Creation\LaserOrderCreation;
use TheClinicUseCases\Orders\Interfaces\IDataBaseCreateLaserOrder;
use TheClinicUseCases\Privileges\PrivilegesManagement;

class LaserOrderCreationTest extends TestCase
{
    private Generator $faker;

    private DSUser|\Mockery\MockInterface $user;

    private Authentication|\Mockery\MockInterface $authentication;

    private ICalculateLaserOrder|\Mockery\MockInterface $iCalculateLaserOrder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();

        $this->user = $this->makeUser();

        /** @var \TheClinicUseCases\Accounts\Authentication|\Mockery\MockInterface $authentication */
        $this->authentication = Mockery::mock(Authentication::class);
        $this->authentication->shouldReceive("check")->with($this->user);

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

    public function testCreateLaserOrder(): void
    {
        $this->testCreateLaserOrderWithIds(14, 14, "selfLaserOrderCreate");
        $this->testCreateLaserOrderWithIds(14, 15, "laserOrderCreate");
    }

    private function testCreateLaserOrderWithIds(int $userId, int $targetUserId, string $privilege): void
    {
        $gender = "Male";
        $price = 400000;
        $timeConsumption = 600;
        $priceWithoutDiscount = 600000;

        $this->user->shouldReceive("getId")->andReturn($userId);

        /** @var \TheClinicDataStructures\DataStructures\User\DSUser|\Mockery\MockInterface $targetUser */
        $targetUser = $this->makeUser();
        $targetUser->shouldReceive("getGender")->twice()->andReturn($gender);
        $targetUser->shouldReceive("getId")->andReturn($targetUserId);

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, $privilege);

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

        $order = (new LaserOrderCreation(
            $this->authentication,
            $privilegesManagement,
            $this->iCalculateLaserOrder,
            $this->iLaserPriceCalculator,
            $this->iLaserTimeConsumptionCalculator
        ))
            ->createLaserOrder($targetUser, $this->user, $dsParts, $dsPackages, $db);
        $this->assertInstanceOf(DSLaserOrder::class, $order);

        try {
            $dsParts = null;
            /** @var \TheClinicDataStructures\DataStructures\Order\DSPackages|\Mockery\MockInterface $dsPackages */
            $dsPackages = Mockery::mock(DSPackages::class);
            $dsPackages->shouldReceive("getGender")->once()->andReturn("Female");

            $order = (new LaserOrderCreation(
                $this->authentication,
                $privilegesManagement,
                $this->iCalculateLaserOrder,
                $this->iLaserPriceCalculator,
                $this->iLaserTimeConsumptionCalculator
            ))
                ->createLaserOrder($targetUser, $this->user, $dsParts, $dsPackages, $db);

            throw new \RuntimeException("Failure!!!", 500);
        } catch (InvalidGenderException $th) {
        }

        try {
            $dsParts = null;
            $dsPackages = null;

            $order = (new LaserOrderCreation(
                $this->authentication,
                $privilegesManagement,
                $this->iCalculateLaserOrder,
                $this->iLaserPriceCalculator,
                $this->iLaserTimeConsumptionCalculator
            ))
                ->createLaserOrder($targetUser, $this->user, $dsParts, $dsPackages, $db);

            throw new \RuntimeException("Failure!!!", 500);
        } catch (NoPackageOrPartException $th) {
        }
    }
}
