<?php

namespace Tests\Orders\Deletion;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use Tests\TestCase;
use TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrder;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Orders\Deletion\RegularOrderDeletion;
use TheClinicUseCases\Orders\Interfaces\IDataBaseDeleteRegularOrder;
use TheClinicUseCases\Privileges\PrivilegesManagement;

class RegularOrderDeletionTest extends TestCase
{
    private Generator $faker;

    private DSUser|\Mockery\MockInterface $user;

    private Authentication|\Mockery\MockInterface $authentication;

    protected function setUp(): void
    {
        parent::setUp();

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

    public function testDeleteRegularOrder(): void
    {
        $this->deleteRegularOrderTester(14, 14, "selfRegularOrderDelete");
        $this->deleteRegularOrderTester(14, 15, "regularOrderDelete");
    }

    private function deleteRegularOrderTester(int $userId, int $anotherUserId, string $privilege): void
    {
        $this->user->shouldReceive("getId")->andReturn($userId);

        /** @var \TheClinicDataStructures\DataStructures\User\DSUser|\Mockery\MockInterface $anotherUser */
        $anotherUser = Mockery::mock(DSUser::class);
        $anotherUser->shouldReceive("getId")->andReturn($anotherUserId);

        /** @var \TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrder|\Mockery\MockInterface $regularOrder */
        $regularOrder = Mockery::mock(DSRegularOrder::class);
        $regularOrder->shouldReceive("getUser")->andReturn($anotherUser);
        /** @var \TheClinicUseCases\Orders\Interfaces\IDataBaseDeleteRegularOrder|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseDeleteRegularOrder::class);
        $db->shouldReceive("deleteRegularOrder")->with($regularOrder, $this->user)->andReturn(null);

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, $privilege);

        $result = (new RegularOrderDeletion($this->authentication, $privilegesManagement))->deleteRegularOrder($regularOrder, $this->user, $db);
        $this->assertNull($result);
    }
}
