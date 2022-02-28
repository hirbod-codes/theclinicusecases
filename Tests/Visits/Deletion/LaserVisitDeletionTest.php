<?php

namespace Tests\visits\Creation;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use Tests\TestCase;
use TheClinicDataStructures\DataStructures\Order\Laser\DSLaserOrder;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicDataStructures\DataStructures\Visit\Laser\DSLaserVisit;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Privileges\PrivilegesManagement;
use TheClinicUseCases\Visits\Deletion\LaserVisitDeletion;
use TheClinicUseCases\Visits\Interfaces\IDataBaseDeleteVisit;

class LaserVisitDeletionTest extends TestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();
    }

    public function testDelete(): void
    {
        $this->deleteTester(14, 14, "selfLaserVisitDelete");
        $this->deleteTester(14, 15, "laserVisitDelete");
    }

    public function deleteTester(int $userId, int $targetUserId, string $privilege): void
    {
        /** @var \TheClinicDataStructures\DataStructures\User\DSUser|\Mockery\MockInterface $user */
        $user = Mockery::mock(DSUser::class);
        $user->shouldReceive("getId")->andReturn($userId);

        /** @var \TheClinicUseCases\Accounts\Authentication|\Mockery\MockInterface $authentication */
        $authentication = Mockery::mock(Authentication::class);
        $authentication->shouldReceive("check")->with($user);
        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegeManagement */
        $privilegeManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegeManagement->shouldReceive("checkBool")->with($user, $privilege);


        /** @var \TheClinicDataStructures\DataStructures\User\DSUser|\Mockery\MockInterface $targetUser */
        $targetUser = Mockery::mock(DSUser::class);
        $targetUser->shouldReceive("getId")->andReturn($targetUserId);

        /** @var \TheClinicDataStructures\DataStructures\Order\Laser\DSLaserOrder|\Mockery\MockInterface $dsLaserOrder */
        $dsLaserOrder = Mockery::mock(DSLaserOrder::class);
        $dsLaserOrder->shouldReceive("getUser")->andReturn($targetUser);

        /** @var \TheClinicDataStructures\DataStructures\Visit\Laser\DSLaserVisit|\Mockery\MockInterface $dsLaserVisit */
        $dsLaserVisit = Mockery::mock(DSLaserVisit::class);
        $dsLaserVisit->shouldReceive("getOrder")->andReturn($dsLaserOrder);

        /** @var \TheClinicUseCases\Visits\Interfaces\IDataBaseDeleteVisit|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseDeleteVisit::class);
        $db->shouldReceive("deleteLaserVisit")->with($dsLaserVisit);

        $result = (new LaserVisitDeletion($authentication, $privilegeManagement))->delete($dsLaserVisit, $user, $db);
        $this->assertNull($result);
    }
}
