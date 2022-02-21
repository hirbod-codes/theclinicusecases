<?php

namespace Tests\Accounts;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use PHPUnit\Framework\TestCase;
use TheClinic\DataStructures\Order\DSLaserOrders;
use TheClinic\DataStructures\Order\DSOrders;
use TheClinic\DataStructures\User\DSUser;
use TheClinic\DataStructures\User\IUserRule;
use TheClinicUseCases\Exceptions\Accounts\UserIsNotAuthenticated;
use TheClinicUseCases\Exceptions\Accounts\UserIsNotAuthorized;
use TheClinicUseCases\Exceptions\PrivilegeNotFound;
use TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveLaserOrders;
use TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveOrders;
use TheClinicUseCases\Orders\OrderManagement;

class OrderManagementTest extends TestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        $this->faker = Factory::create();
    }

    public function testGetOrders(): void
    {
        $lastOrderId = $this->faker->numberBetween(1, 1000);
        $count = $this->faker->numberBetween(1, 30);

        /** @var \TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveOrders|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseRetrieveOrders::class);
        $db->shouldReceive("getOrders")->with($lastOrderId, $count)->andReturn(new DSOrders());

        /** @var \TheClinic\DataStructures\User\IUserRule|\Mockery\MockInterface $rule */
        $rule = Mockery::mock(IUserRule::class);
        $rule->shouldReceive("privilegeExists")->with("ordersRead")->andReturn(true);
        $rule->shouldReceive("getPrivilegeValue")->with("ordersRead")->andReturn(true);

        /** @var \TheClinic\DataStructures\User\DSUser|\Mockery\MockInterface $user */
        $user = Mockery::mock(DSUser::class);
        $user->shouldReceive("isAuthenticated")->andReturn(true);
        $user->shouldReceive("getRole")->andReturn($rule);

        $orders = (new OrderManagement)->getOrders($user, $lastOrderId, $count, $db);
        $this->assertInstanceOf(DSOrders::class, $orders);

        try {
            /** @var \TheClinic\DataStructures\User\IUserRule|\Mockery\MockInterface $rule */
            $rule = Mockery::mock(IUserRule::class);
            $rule->shouldReceive("privilegeExists")->with("ordersRead")->andReturn(false);
            $rule->shouldReceive("getPrivilegeValue")->with("ordersRead")->andReturn(true);

            /** @var \TheClinic\DataStructures\User\DSUser|\Mockery\MockInterface $user */
            $user = Mockery::mock(DSUser::class);
            $user->shouldReceive("isAuthenticated")->andReturn(true);
            $user->shouldReceive("getRole")->andReturn($rule);

            (new OrderManagement)->getOrders($user, $lastOrderId, $count, $db);
            throw new \RuntimeException("Failure!!!", 500);
        } catch (PrivilegeNotFound $th) {
            $this->assertEquals("There is no such privilege.", $th->getMessage());
        }

        try {
            /** @var \TheClinic\DataStructures\User\IUserRule|\Mockery\MockInterface $rule */
            $rule = Mockery::mock(IUserRule::class);
            $rule->shouldReceive("privilegeExists")->with("ordersRead")->andReturn(true);
            $rule->shouldReceive("getPrivilegeValue")->with("ordersRead")->andReturn(false);

            /** @var \TheClinic\DataStructures\User\DSUser|\Mockery\MockInterface $user */
            $user = Mockery::mock(DSUser::class);
            $user->shouldReceive("isAuthenticated")->andReturn(true);
            $user->shouldReceive("getRole")->andReturn($rule);

            (new OrderManagement)->getOrders($user, $lastOrderId, $count, $db);
            throw new \RuntimeException("Failure!!!", 500);
        } catch (UserIsNotAuthorized $th) {
            $this->assertEquals("The current authenticated user is not authorized for this action.", $th->getMessage());
        }

        try {
            /** @var \TheClinic\DataStructures\User\IUserRule|\Mockery\MockInterface $rule */
            $rule = Mockery::mock(IUserRule::class);
            $rule->shouldReceive("privilegeExists")->with("ordersRead")->andReturn(true);
            $rule->shouldReceive("getPrivilegeValue")->with("ordersRead")->andReturn(true);

            /** @var \TheClinic\DataStructures\User\DSUser|\Mockery\MockInterface $user */
            $user = Mockery::mock(DSUser::class);
            $user->shouldReceive("isAuthenticated")->andReturn(false);
            $user->shouldReceive("getRole")->andReturn($rule);

            (new OrderManagement)->getOrders($user, $lastOrderId, $count, $db);
            throw new \RuntimeException("Failure!!!", 500);
        } catch (UserIsNotAuthenticated $th) {
            $this->assertEquals("The current authenticated user is not authenticated.", $th->getMessage());
        }
    }

    public function testGetLaserOrders(): void
    {
        $lastOrderId = $this->faker->numberBetween(1, 1000);
        $count = $this->faker->numberBetween(1, 30);

        /** @var \TheClinicUseCases\Orders\Interfaces\IDataBaseRetrieveLaserOrders|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseRetrieveLaserOrders::class);
        $db->shouldReceive("getLaserOrders")->with($lastOrderId, $count)->andReturn(new DSLaserOrders());

        /** @var \TheClinic\DataStructures\User\IUserRule|\Mockery\MockInterface $rule */
        $rule = Mockery::mock(IUserRule::class);
        $rule->shouldReceive("privilegeExists")->with("laserOrdersRead")->andReturn(true);
        $rule->shouldReceive("getPrivilegeValue")->with("laserOrdersRead")->andReturn(true);

        /** @var \TheClinic\DataStructures\User\DSUser|\Mockery\MockInterface $user */
        $user = Mockery::mock(DSUser::class);
        $user->shouldReceive("isAuthenticated")->andReturn(true);
        $user->shouldReceive("getRole")->andReturn($rule);

        $orders = (new OrderManagement)->getLaserOrders($user, $lastOrderId, $count, $db);
        $this->assertInstanceOf(DSOrders::class, $orders);

        try {
            /** @var \TheClinic\DataStructures\User\IUserRule|\Mockery\MockInterface $rule */
            $rule = Mockery::mock(IUserRule::class);
            $rule->shouldReceive("privilegeExists")->with("laserOrdersRead")->andReturn(false);
            $rule->shouldReceive("getPrivilegeValue")->with("laserOrdersRead")->andReturn(true);

            /** @var \TheClinic\DataStructures\User\DSUser|\Mockery\MockInterface $user */
            $user = Mockery::mock(DSUser::class);
            $user->shouldReceive("isAuthenticated")->andReturn(true);
            $user->shouldReceive("getRole")->andReturn($rule);

            (new OrderManagement)->getLaserOrders($user, $lastOrderId, $count, $db);
            throw new \RuntimeException("Failure!!!", 500);
        } catch (PrivilegeNotFound $th) {
            $this->assertEquals("There is no such privilege.", $th->getMessage());
        }

        try {
            /** @var \TheClinic\DataStructures\User\IUserRule|\Mockery\MockInterface $rule */
            $rule = Mockery::mock(IUserRule::class);
            $rule->shouldReceive("privilegeExists")->with("laserOrdersRead")->andReturn(true);
            $rule->shouldReceive("getPrivilegeValue")->with("laserOrdersRead")->andReturn(false);

            /** @var \TheClinic\DataStructures\User\DSUser|\Mockery\MockInterface $user */
            $user = Mockery::mock(DSUser::class);
            $user->shouldReceive("isAuthenticated")->andReturn(true);
            $user->shouldReceive("getRole")->andReturn($rule);

            (new OrderManagement)->getLaserOrders($user, $lastOrderId, $count, $db);
            throw new \RuntimeException("Failure!!!", 500);
        } catch (UserIsNotAuthorized $th) {
            $this->assertEquals("The current authenticated user is not authorized for this action.", $th->getMessage());
        }

        try {
            /** @var \TheClinic\DataStructures\User\IUserRule|\Mockery\MockInterface $rule */
            $rule = Mockery::mock(IUserRule::class);
            $rule->shouldReceive("privilegeExists")->with("laserOrdersRead")->andReturn(true);
            $rule->shouldReceive("getPrivilegeValue")->with("laserOrdersRead")->andReturn(true);

            /** @var \TheClinic\DataStructures\User\DSUser|\Mockery\MockInterface $user */
            $user = Mockery::mock(DSUser::class);
            $user->shouldReceive("isAuthenticated")->andReturn(false);
            $user->shouldReceive("getRole")->andReturn($rule);

            (new OrderManagement)->getLaserOrders($user, $lastOrderId, $count, $db);
            throw new \RuntimeException("Failure!!!", 500);
        } catch (UserIsNotAuthenticated $th) {
            $this->assertEquals("The current authenticated user is not authenticated.", $th->getMessage());
        }
    }
}
