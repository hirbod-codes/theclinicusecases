<?php

namespace Tests\Orders\Craetion;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use Tests\TestCase;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinic\Order\ICalculateRegularOrder;
use TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrder;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Orders\Creation\RegularOrderCreation;
use TheClinicUseCases\Orders\Interfaces\IDataBaseCreateRegularOrder;
use TheClinicUseCases\Privileges\PrivilegesManagement;

class RegularOrderCreationTest extends TestCase
{
    private Generator $faker;

    private DSUser|\Mockery\MockInterface $user;

    private Authentication|\Mockery\MockInterface $authentication;

    private ICalculateRegularOrder|\Mockery\MockInterface $iCalculateRegularOrder;

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

        $order = (new RegularOrderCreation(
            $this->authentication,
            $privilegesManagement,
            $this->iCalculateRegularOrder
        ))
            ->createRegularOrder($targetUser, $this->user, $db);
        $this->assertInstanceOf(DSRegularOrder::class, $order);
    }
}
