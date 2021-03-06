<?php

namespace Tests\visits\Creation;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;
use Tests\Traits\MakeAuthenticatable;
use TheClinic\Visit\IFindVisit;
use TheClinicDataStructures\DataStructures\Order\Laser\DSLaserOrder;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicDataStructures\DataStructures\Visit\Laser\DSLaserVisit;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Privileges\PrivilegesManagement;
use TheClinicUseCases\Visits\Creation\LaserVisitCreation;
use TheClinicUseCases\Visits\Interfaces\IDataBaseCreateLaserVisit;

class LaserVisitCreationTest extends TestCase
{
    use MakeAuthenticatable;

    private Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();
    }

    public function testCreate(): void
    {
        $user = $this->makeAuthenticatable(true);

        $targetUser = $this->makeAuthenticatable();

        $this->createTester($user, $user, "selfLaserVisitCreate");
        $this->createTester($user, $targetUser, "laserVisitCreate");
    }

    public function createTester(DSUser $user, DSUser $targetUser, string $privilege): void
    {
        $timestamp = $this->faker->numerify("#########");

        /** @var Authentication|MockInterface $authentication */
        $authentication = Mockery::mock(Authentication::class);
        $authentication->shouldReceive("check")->with($user);
        /** @var PrivilegesManagement|MockInterface $privilegeManagement */
        $privilegeManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegeManagement->shouldReceive("checkBool")->with($user, $privilege);

        /** @var DSLaserOrder|MockInterface $dsLaserOrder */
        $dsLaserOrder = Mockery::mock(DSLaserOrder::class);

        /** @var DSLaserVisit|MockInterface $dsLaserVisit */
        $dsLaserVisit = Mockery::mock(DSLaserVisit::class);

        /** @var IFindVisit|MockInterface $iFindVisit */
        $iFindVisit = Mockery::mock(IFindVisit::class);

        /** @var IDataBaseCreateLaserVisit|MockInterface $db */
        $db = Mockery::mock(IDataBaseCreateLaserVisit::class);
        $db
            ->shouldReceive("createLaserVisit")
            ->with($dsLaserOrder, $targetUser, $iFindVisit)
            ->andReturn($dsLaserVisit);

        $createdVisit = (new LaserVisitCreation($authentication, $privilegeManagement))
            ->create($dsLaserOrder, $targetUser, $user, $db, $iFindVisit);
        $this->assertInstanceOf(DSLaserVisit::class, $createdVisit);
    }
}
