<?php

namespace Tests\Privileges;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;
use TheClinicDataStructures\DataStructures\User\DSAdmin;
use TheClinicDataStructures\DataStructures\User\DSPatient;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicDataStructures\DataStructures\User\ICheckAuthentication;
use TheClinicDataStructures\DataStructures\User\Interfaces\IPrivilege;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Exceptions\Accounts\AdminTemptsToSetAdminPrivilege;
use TheClinicUseCases\Exceptions\Accounts\UserIsNotAuthorized;
use TheClinicUseCases\Exceptions\PrivilegeNotFound;
use TheClinicUseCases\Privileges\Interfaces\IDataBaseCreateRole;
use TheClinicUseCases\Privileges\Interfaces\IDataBaseDeleteRole;
use TheClinicUseCases\Privileges\PrivilegesManagement;

class PrivilegesManagementTest extends TestCase
{
    private Generator $faker;

    private DSUser|MockInterface $user;

    private DSUser|MockInterface $dsUser;

    private DSUser|MockInterface $authenticated;

    private Authentication|MockInterface $authentication;

    private bool $first = true;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();

        $this->authenticated = $this->makeAuthenticatable(true);

        $this->dsUser = $this->makeAuthenticatable(false);

        /** @var Authentication|\Mockery\MockInterface $authentication */
        $this->authentication = Mockery::mock(Authentication::class);
    }

    private function makeAuthenticatable($admin = false): DSUser
    {
        /** @var IcheckAuthentication|\Mockery\MockInterface $icheckAuthentication */
        $icheckAuthentication = Mockery::mock(ICheckAuthentication::class);

        if ($admin === true) {
            return new DSAdmin(
                $icheckAuthentication,
                $this->faker->numberBetween(1, 100),
                $this->faker->firstName(),
                $this->faker->lastNAme(),
                $this->faker->userName(),
                $this->faker->randomElement(['Male', 'Female']),
                $this->faker->phoneNumber(),
                new \DateTime,
                new \DateTime,
                new \DateTime,
                $this->faker->safeEmail(),
                new \DateTime,
                null,
                null
            );
        } else {
            return new DSPatient(
                $icheckAuthentication,
                $this->faker->numberBetween(1, 100),
                $this->faker->firstName(),
                $this->faker->lastNAme(),
                $this->faker->userName(),
                $this->faker->randomElement(['Male', 'Female']),
                $this->faker->phoneNumber(),
                new \DateTime,
                new \DateTime,
                new \DateTime,
                $this->faker->safeEmail(),
                new \DateTime,
                null,
                null
            );
        }
    }

    private function instantiate(): PrivilegesManagement
    {
        return new PrivilegesManagement($this->authentication);
    }

    public function testGetPrivileges(): void
    {
        $this->authentication->shouldReceive('check')->with($this->authenticated);

        $array = $this->instantiate()->getPrivileges($this->authenticated);

        $privileges = DSUser::getPrivileges();

        foreach ($array as $key => $value) {
            $this->assertNotFalse(array_search($key, array_keys($privileges)));
            $this->assertEquals($privileges[$key], $value);
        }
    }

    public function testCreateRole(): void
    {
        $privilegeValue = ['privilege', 'value'];
        $this->authentication->shouldReceive('check')->with($this->authenticated);

        /** @var IDataBaseCreateRole|MockInterface $iDataBaseCreateRole */
        $iDataBaseCreateRole = Mockery::mock(IDataBaseCreateRole::class);
        $iDataBaseCreateRole
            ->shouldReceive('createRole')
            ->with('custom', $privilegeValue);

        $result = $this->instantiate()->createRole($this->authenticated, 'custom', $privilegeValue, $iDataBaseCreateRole);
        $this->assertNull($result);
    }

    public function testDeleteRole(): void
    {
        $this->authentication->shouldReceive('check')->with($this->authenticated);

        /** @var IDataBaseDeleteRole|MockInterface $iDataBaseDeleteRole */
        $iDataBaseDeleteRole = Mockery::mock(IDataBaseDeleteRole::class);
        $iDataBaseDeleteRole
            ->shouldReceive('deleteRole')
            ->with('custom');

        $result = $this->instantiate()->deleteRole($this->authenticated, 'custom', $iDataBaseDeleteRole);
        $this->assertNull($result);
    }

    public function testGetUserPrivileges(): void
    {
        $this->authentication->shouldReceive('check')->with($this->authenticated);

        $privileges = $this->dsUser::getUserPrivileges();

        $array = $this->instantiate()->getUserPrivileges($this->authenticated, $this->dsUser);

        $this->assertIsArray($array);
        $this->assertCount(count($privileges), $array);

        foreach ($privileges as $key => $value) {
            $this->assertNotFalse(array_search($key, array_keys($array)));
            $this->assertEquals($value, $array[$key]);
        }
    }

    public function testGetSelfPrivileges(): void
    {
        $this->authentication->shouldReceive('check')->with($this->dsUser);

        $privileges = $this->dsUser::getUserPrivileges();

        $array = $this->instantiate()->getSelfPrivileges($this->dsUser);
        $this->assertIsArray($array);
        $this->assertCount(count($privileges), $array);

        foreach ($privileges as $key => $value) {
            $this->assertNotFalse(array_search($key, array_keys($array)));
            $this->assertEquals($value, $array[$key]);
        }
    }

    public function testSetUserPrivilege(): void
    {
        $privilege = "selfAccountRead";

        /** @var IPrivilege|MockInterface $ip */
        $ip = Mockery::mock(IPrivilege::class);

        $this->authentication->shouldReceive('check')->with($this->authenticated);

        /** @var DSUser|MockInterface $dsUser */
        $dsUser = Mockery::mock(DSUser::class);
        $dsUser->shouldReceive('setPrivilege')->with($privilege, false, $ip);

        $instance = $this->instantiate();
        $result = $instance->setUserPrivilege($this->authenticated, $dsUser, $privilege, false, $ip);

        $this->assertNull($result);

        try {
            $result = $instance->setUserPrivilege($this->authenticated, $this->makeAuthenticatable(true), $privilege, false, $ip);
            throw new \RuntimeException('Failure!!!');
        } catch (AdminTemptsToSetAdminPrivilege $th) {
        }
    }

    public function testCheckBool(): void
    {
        $privilege = "ExampleBooleanPrivilege";

        /** @var \TheClinicDataStructures\DataStructures\User\DSUser|\Mockery\MockInterface $user */
        $this->user = Mockery::mock(DSUser::class);
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
