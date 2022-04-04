<?php

namespace Tests\visits\Creation;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use Tests\TestCase;
use TheClinic\Visit\IFindVisit;
use TheClinicDataStructures\DataStructures\Order\Laser\DSLaserOrder;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicDataStructures\DataStructures\Visit\Laser\DSLaserVisit;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Privileges\PrivilegesManagement;
use TheClinicUseCases\Visits\Creation\LaserVisitCreation;
use TheClinicUseCases\Visits\Interfaces\IDataBaseCreateVisit;

class LaserVisitCreationTest extends TestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();
    }

    public function testCreate(): void
    {
        $this->createTester(14, 14, "selfLaserVisitCreate");
        $this->createTester(14, 15, "laserVisitCreate");
    }

    public function createTester(int $userId, int $targetUserId, string $privilege): void
    {
        $timestamp = $this->faker->numerify("#########");

        /** @var \TheClinicDataStructures\DataStructures\User\DSUser|\Mockery\MockInterface $user */
        $user = Mockery::mock(DSUser::class);
        $user->shouldReceive("getId")->andReturn($userId);

        /** @var \TheClinicDataStructures\DataStructures\User\DSUser|\Mockery\MockInterface $targetUser */
        $targetUser = Mockery::mock(DSUser::class);
        $targetUser->shouldReceive("getId")->andReturn($targetUserId);

        /** @var \TheClinicUseCases\Accounts\Authentication|\Mockery\MockInterface $authentication */
        $authentication = Mockery::mock(Authentication::class);
        $authentication->shouldReceive("check")->with($user);
        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegeManagement */
        $privilegeManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegeManagement->shouldReceive("checkBool")->with($user, $privilege);

        /** @var \TheClinicDataStructures\DataStructures\Order\Laser\DSLaserOrder|\Mockery\MockInterface $dsLaserOrder */
        $dsLaserOrder = Mockery::mock(DSLaserOrder::class);

        /** @var \TheClinicDataStructures\DataStructures\Visit\Laser\DSLaserVisit|\Mockery\MockInterface $dsLaserVisit */
        $dsLaserVisit = Mockery::mock(DSLaserVisit::class);

        /** @var \TheClinic\Visit\IFindVisit|\Mockery\MockInterface $iFindVisit */
        $iFindVisit = Mockery::mock(IFindVisit::class);
        $iFindVisit->shouldReceive("findVisit")->andReturn($timestamp);

        /** @var \TheClinicUseCases\Visits\Interfaces\IDataBaseCreateVisit|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseCreateVisit::class);
        $db->shouldReceive("createLaserVisit")->with($dsLaserOrder, $targetUser, $timestamp)->andReturn($dsLaserVisit);

        $createdVisit = (new LaserVisitCreation($authentication, $privilegeManagement, $iFindVisit))->create($dsLaserOrder, $targetUser, $user, $db);
        $this->assertInstanceOf(DSLaserVisit::class, $createdVisit);
    }
}
