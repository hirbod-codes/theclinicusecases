<?php

namespace Tests\Privileges;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use Tests\TestCase;
use TheClinic\DataStructures\User\DSUser;
use TheClinic\DataStructures\User\IUserRule;
use TheClinicUseCases\Exceptions\Accounts\UserIsNotAuthorized;
use TheClinicUseCases\Exceptions\PrivilegeNotFound;
use TheClinicUseCases\Privileges\PrivilegesManagement;

class PrivilegesManagementTest extends TestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        $this->faker = Factory::create();
    }

    public function testCheckBool(): void
    {
        $privilege = "ExampleBooleanPrivilege";

        /** @var \TheClinic\DataStructures\User\IUserRule|\Mockery\MockInterface $rule */
        $rule = Mockery::mock(IUserRule::class);
        $rule->shouldReceive("privilegeExists")->with($privilege)->andReturn(true);
        $rule->shouldReceive("getPrivilegeValue")->with($privilege)->andReturn(true);

        /** @var \TheClinic\DataStructures\User\DSUser|\Mockery\MockInterface $dsUser */
        $dsUser = Mockery::mock(DSUser::class);
        $dsUser->shouldReceive("getRule")->andReturn($rule);

        $result = (new PrivilegesManagement)->checkBool($dsUser, $privilege);
        $this->assertNull($result);

        try {
            /** @var \TheClinic\DataStructures\User\IUserRule|\Mockery\MockInterface $rule */
            $rule = Mockery::mock(IUserRule::class);
            $rule->shouldReceive("privilegeExists")->with($privilege)->andReturn(false);
            $rule->shouldReceive("getPrivilegeValue")->with($privilege)->andReturn(true);

            /** @var \TheClinic\DataStructures\User\DSUser|\Mockery\MockInterface $dsUser */
            $dsUser = Mockery::mock(DSUser::class);
            $dsUser->shouldReceive("getRule")->andReturn($rule);

            $result = (new PrivilegesManagement)->checkBool($dsUser, $privilege);
            throw new \RuntimeException("Failure!!!", 500);
        } catch (PrivilegeNotFound $th) {
        }

        try {
            /** @var \TheClinic\DataStructures\User\IUserRule|\Mockery\MockInterface $rule */
            $rule = Mockery::mock(IUserRule::class);
            $rule->shouldReceive("privilegeExists")->with($privilege)->andReturn(true);
            $rule->shouldReceive("getPrivilegeValue")->with($privilege)->andReturn(false);

            /** @var \TheClinic\DataStructures\User\DSUser|\Mockery\MockInterface $dsUser */
            $dsUser = Mockery::mock(DSUser::class);
            $dsUser->shouldReceive("getRule")->andReturn($rule);

            $result = (new PrivilegesManagement)->checkBool($dsUser, $privilege);
            throw new \RuntimeException("Failure!!!", 500);
        } catch (UserIsNotAuthorized $th) {
        }
    }
}
