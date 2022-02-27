<?php

namespace Tests\Privileges;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicUseCases\Exceptions\Accounts\UserIsNotAuthorized;
use TheClinicUseCases\Exceptions\PrivilegeNotFound;
use TheClinicUseCases\Privileges\PrivilegesManagement;

class PrivilegesManagementTest extends TestCase
{
    private Generator $faker;

    private DSUser|MockInterface $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();

        /** @var \TheClinicDataStructures\DataStructures\User\DSUser|\Mockery\MockInterface $user */
        $this->user = Mockery::mock(DSUser::class);
    }

    public function testGetUserPrivileges(): void
    {
        $this->user->shouldReceive("getUserPrivileges")->andReturn([]);

        $privileges = (new PrivilegesManagement)->getUserPrivileges($this->user);

        $this->assertIsArray($privileges);
        $this->assertCount(0, $privileges);
    }

    public function testGetUserPrivilege(): void
    {
        $privilege = "privilege";

        $this->user->shouldReceive("getPrivilege")->with($privilege)->andReturn(true);

        $privilege = (new PrivilegesManagement)->getUserPrivilege($this->user, $privilege);

        $this->assertIsBool($privilege);
        $this->assertEquals(true, $privilege);
    }

    public function testSetUserPrivilege(): void
    {
        $privilege = "privilege";
        $value = true;

        $this->user->shouldReceive("setPrivilege")->with($privilege, $value)->andReturn(null);

        $result = (new PrivilegesManagement)->setUserPrivilege($this->user, $privilege, $value);

        $this->assertNull($result);
    }

    public function testCheckBool(): void
    {
        $privilege = "ExampleBooleanPrivilege";

        $this->user->shouldReceive("privilegeExists")->with($privilege)->andReturn(true);
        $this->user->shouldReceive("getPrivilege")->with($privilege)->andReturn(true);

        $result = (new PrivilegesManagement)->checkBool($this->user, $privilege);
        $this->assertNull($result);

        try {
            /** @var \TheClinicDataStructures\DataStructures\User\DSUser|\Mockery\MockInterface $user */
            $this->user = Mockery::mock(DSUser::class);
            $this->user->shouldReceive("privilegeExists")->with($privilege)->andReturn(false);
            $this->user->shouldReceive("getPrivilege")->with($privilege)->andReturn(true);

            $result = (new PrivilegesManagement)->checkBool($this->user, $privilege);
            throw new \RuntimeException("Failure!!!", 500);
        } catch (PrivilegeNotFound $th) {
        }

        try {
            /** @var \TheClinicDataStructures\DataStructures\User\DSUser|\Mockery\MockInterface $user */
            $this->user = Mockery::mock(DSUser::class);
            $this->user->shouldReceive("privilegeExists")->with($privilege)->andReturn(true);
            $this->user->shouldReceive("getPrivilege")->with($privilege)->andReturn(false);

            $result = (new PrivilegesManagement)->checkBool($this->user, $privilege);
            throw new \RuntimeException("Failure!!!", 500);
        } catch (UserIsNotAuthorized $th) {
        }
    }
}
