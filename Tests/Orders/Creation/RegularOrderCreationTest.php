<?php

namespace Tests\Orders\Craetion;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrder;
use TheClinicDataStructures\DataStructures\User\DSAdmin;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Exceptions\AdminsCollisionException;
use TheClinicUseCases\Exceptions\AdminModificationByUserException;
use TheClinicUseCases\Orders\Creation\RegularOrderCreation;
use TheClinicUseCases\Orders\Interfaces\IDataBaseCreateDefaultRegularOrder;
use TheClinicUseCases\Orders\Interfaces\IDataBaseCreateRegularOrder;
use TheClinicUseCases\Privileges\PrivilegesManagement;

class RegularOrderCreationTest extends TestCase
{
    private Generator $faker;

    private Authentication|\Mockery\MockInterface $authentication;

    private PrivilegesManagement|\Mockery\MockInterface $privilegesManagement;

    private IDataBaseCreateRegularOrder|IDataBaseCreateDefaultRegularOrder|\Mockery\MockInterface $db;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();

        $this->user = $this->makeUser();
    }

    private function makeUser(): DSUser|MockInterface
    {
        /** @var \TheClinicDataStructures\DataStructures\User\DSUser|\Mockery\MockInterface $user */
        $user = Mockery::mock(DSUser::class);
        return $user;
    }

    private function makeAdmin(): DSAdmin|MockInterface
    {
        /** @var \TheClinicDataStructures\DataStructures\User\DSAdmin|\Mockery\MockInterface $admin */
        $admin = Mockery::mock(DSAdmin::class);
        return $admin;
    }

    public function testCreateRegularOrder(): void
    {
        $this->testCreateRegularOrderWithIds(14, 14);
        $this->testCreateRegularOrderWithIds(14, 15);
    }

    private function testCreateRegularOrderWithIds(int $userId, int $targetUserId)
    {
        $user = $this->makeAdmin();
        $user->shouldReceive("getId")->andReturn($userId);

        $targetUser = $this->makeAdmin();
        $targetUser->shouldReceive("getId")->andReturn($targetUserId);

        if ($userId === $targetUserId) {
            $privilege = "selfRegularOrderCreate";
        } else {
            $privilege = "regularOrderCreate";
        }

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($user, $privilege);

        /** @var \TheClinicUseCases\Accounts\Authentication|\Mockery\MockInterface $authentication */
        $authentication = Mockery::mock(Authentication::class);
        $authentication->shouldReceive("check")->with($user);

        $price = 400000;
        $timeConsumption = 600;

        /** @var DSRegularOrder|\Mockery\MockInterface $dsOrder */
        $dsOrder = Mockery::mock(DSRegularOrder::class);
        /** @var IDataBaseCreateRegularOrder|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseCreateRegularOrder::class);
        $db->shouldReceive("createRegularOrder")->with($targetUser, $price, $timeConsumption)->andReturn($dsOrder);

        if ($userId !== $targetUserId) {
            try {
                (new RegularOrderCreation($authentication, $privilegesManagement))->createRegularOrder($price, $timeConsumption, $targetUser, $user, $db);
                throw new \RuntimeException("Failure!!!", 1);
            } catch (AdminsCollisionException $e) {
            }
        } else {
            $createdOrder = (new RegularOrderCreation($authentication, $privilegesManagement))->createRegularOrder($price, $timeConsumption, $targetUser, $user, $db);
            $this->assertInstanceOf(DSRegularOrder::class, $createdOrder);
        }

        $targetUser = $this->makeUser();
        $targetUser->shouldReceive("getId")->andReturn($targetUserId);

        /** @var IDataBaseCreateRegularOrder|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseCreateRegularOrder::class);
        $db->shouldReceive("createRegularOrder")->with($targetUser, $price, $timeConsumption)->andReturn($dsOrder);

        $createdOrder = (new RegularOrderCreation($authentication, $privilegesManagement))->createRegularOrder($price, $timeConsumption, $targetUser, $user, $db);
        $this->assertInstanceOf(DSRegularOrder::class, $createdOrder);

        $createdOrder = (new RegularOrderCreation($authentication, $privilegesManagement))->createRegularOrder($price, $timeConsumption, $targetUser, $user, $db);
        $this->assertInstanceOf(DSRegularOrder::class, $createdOrder);
    }

    public function testCreateDefaultRegularOrder(): void
    {
        $this->testCreateDefaultRegularOrderWithIds(14, 14);
        $this->testCreateDefaultRegularOrderWithIds(14, 15);
    }

    private function testCreateDefaultRegularOrderWithIds(int $userId, int $targetUserId)
    {
        if ($userId === $targetUserId) {
            $privilege = "selfRegularOrderCreate";
        } else {
            $privilege = "regularOrderCreate";
        }

        $this->doConditionalTests(
            $this->setUpUser(true, $userId, $privilege),
            $userId,
            $this->setUpTargetUser(true, $targetUserId),
            $targetUserId
        );

        $this->doConditionalTests(
            $this->setUpUser(false, $userId, $privilege),
            $userId,
            $this->setUpTargetUser(false, $targetUserId),
            $targetUserId
        );

        $this->doConditionalTests(
            $this->setUpUser(true, $userId, $privilege),
            $userId,
            $this->setUpTargetUser(false, $targetUserId),
            $targetUserId
        );

        $this->doConditionalTests(
            $this->setUpUser(false, $userId, $privilege),
            $userId,
            $this->setUpTargetUser(true, $targetUserId),
            $targetUserId
        );
    }

    private function setUpUser(bool $admin, int $userId, string $privilege): DSUser
    {
        if ($admin) {
            $user = $this->makeAdmin();
        } else {
            $user = $this->makeUser();
        }
        $user->shouldReceive("getId")->andReturn($userId);

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $this->privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $this->privilegesManagement->shouldReceive("checkBool")->with($user, $privilege);

        /** @var \TheClinicUseCases\Accounts\Authentication|\Mockery\MockInterface $authentication */
        $this->authentication = Mockery::mock(Authentication::class);
        $this->authentication->shouldReceive("check")->with($user);

        return $user;
    }

    private function setUpTargetUser(bool $admin, int $targetUserId): DSUser
    {
        if ($admin) {
            $targetUser = $this->makeAdmin();
        } else {
            $targetUser = $this->makeUser();
        }
        $targetUser->shouldReceive("getId")->andReturn($targetUserId);

        /** @var DSRegularOrder|\Mockery\MockInterface $dsOrder */
        $dsOrder = Mockery::mock(DSRegularOrder::class);
        /** @var IDataBaseCreateDefaultRegularOrder|\Mockery\MockInterface $db */
        $this->db = Mockery::mock(IDataBaseCreateDefaultRegularOrder::class);
        $this->db->shouldReceive("createDefaultRegularOrder")->with($targetUser)->andReturn($dsOrder);

        return $targetUser;
    }

    private function doConditionalTests(
        DSUser $user,
        int $userId,
        DSUser $targetUser,
        int $targetUserId
    ): void {
        if ($userId === $targetUserId && get_class($user) !== get_class($targetUser)) {
            return;
        }

        if ($userId === $targetUserId) {
            $createdOrder = (new RegularOrderCreation($this->authentication, $this->privilegesManagement))->createDefaultRegularOrder($targetUser, $user, $this->db);
            $this->assertInstanceOf(DSRegularOrder::class, $createdOrder);
            return;
        }

        if ($targetUser instanceof DSAdmin) {
            if ($user instanceof DSAdmin) {
                try {
                    (new RegularOrderCreation($this->authentication, $this->privilegesManagement))->createDefaultRegularOrder($targetUser, $user, $this->db);
                    throw new \RuntimeException("Failure!!!", 1);
                } catch (AdminsCollisionException $e) {
                }
            } else {
                try {
                    (new RegularOrderCreation($this->authentication, $this->privilegesManagement))->createDefaultRegularOrder($targetUser, $user, $this->db);
                    throw new \RuntimeException("Failure!!!", 1);
                } catch (AdminModificationByUserException $e) {
                }
            }
        } else {
            $createdOrder = (new RegularOrderCreation($this->authentication, $this->privilegesManagement))->createDefaultRegularOrder($targetUser, $user, $this->db);
            $this->assertInstanceOf(DSRegularOrder::class, $createdOrder);
        }
    }
}
