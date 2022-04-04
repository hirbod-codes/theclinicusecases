<?php

namespace Tests\visits\Creation;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use Tests\TestCase;
use TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrder;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicDataStructures\DataStructures\Visit\Regular\DSRegularVisit;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Privileges\PrivilegesManagement;
use TheClinicUseCases\Visits\Deletion\RegularVisitDeletion;
use TheClinicUseCases\Visits\Interfaces\IDataBaseDeleteVisit;

class RegularVisitDeletionTest extends TestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();
    }

    public function testDelete(): void
    {
        $this->deleteTester(14, 14, "selfRegularVisitDelete");
        $this->deleteTester(14, 15, "regularVisitDelete");
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

        /** @var \TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrder|\Mockery\MockInterface $dsRegularOrder */
        $dsRegularOrder = Mockery::mock(DSRegularOrder::class);
        $dsRegularOrder->shouldReceive("getUser")->andReturn($targetUser);

        /** @var \TheClinicDataStructures\DataStructures\Visit\Regular\DSRegularVisit|\Mockery\MockInterface $dsRegularVisit */
        $dsRegularVisit = Mockery::mock(DSRegularVisit::class);
        $dsRegularVisit->shouldReceive("getOrder")->andReturn($dsRegularOrder);

        /** @var \TheClinicUseCases\Visits\Interfaces\IDataBaseDeleteVisit|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseDeleteVisit::class);
        $db->shouldReceive("deleteRegularVisit")->with($dsRegularVisit, $targetUser);

        $result = (new RegularVisitDeletion($authentication, $privilegeManagement))->delete($dsRegularVisit, $targetUser, $user, $db);
        $this->assertNull($result);
    }
}
