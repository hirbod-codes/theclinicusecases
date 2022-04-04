<?php

namespace Tests\Orders\Deletion;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use Tests\TestCase;
use TheClinicDataStructures\DataStructures\Order\Laser\DSLaserOrder;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Orders\Deletion\LaserOrderDeletion;
use TheClinicUseCases\Orders\Interfaces\IDataBaseDeleteLaserOrder;
use TheClinicUseCases\Privileges\PrivilegesManagement;

class LaserOrderDeletionTest extends TestCase
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

    public function testDeleteLaserOrder(): void
    {
        $this->deleteLaserOrderTester(14, 14, "selfLaserOrderDelete");
        $this->deleteLaserOrderTester(14, 15, "laserOrderDelete");
    }

    private function deleteLaserOrderTester(int $userId, int $anotherUserId, string $privilege): void
    {
        $this->user->shouldReceive("getId")->andReturn($userId);

        /** @var \TheClinicDataStructures\DataStructures\User\DSUser|\Mockery\MockInterface $anotherUser */
        $anotherUser = Mockery::mock(DSUser::class);
        $anotherUser->shouldReceive("getId")->andReturn($anotherUserId);

        /** @var \TheClinicDataStructures\DataStructures\Order\Laser\DSLaserOrder|\Mockery\MockInterface $laserOrder */
        $laserOrder = Mockery::mock(DSLaserOrder::class);
        /** @var \TheClinicUseCases\Orders\Interfaces\IDataBaseDeleteLaserOrder|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseDeleteLaserOrder::class);
        $db->shouldReceive("deleteLaserOrder")->with($laserOrder, $anotherUser)->andReturn(null);

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, $privilege);

        $result = (new LaserOrderDeletion($this->authentication, $privilegesManagement))->deleteLaserOrder($laserOrder, $anotherUser, $this->user, $db);
        $this->assertNull($result);
    }
}
