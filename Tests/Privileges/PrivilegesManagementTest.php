<?php

namespace Tests\Privileges;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;
use Tests\Traits\MakeAuthenticatable;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicDataStructures\DataStructures\User\Interfaces\IPrivilege;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Exceptions\Accounts\AdminTemptsToSetAdminPrivilege;
use TheClinicUseCases\Exceptions\Accounts\UserIsNotAuthorized;
use TheClinicUseCases\Exceptions\PrivilegeNotFound;
use TheClinicUseCases\Privileges\Interfaces\IDataBaseCreateRole;
use TheClinicUseCases\Privileges\Interfaces\IDataBaseDeleteRole;
use TheClinicUseCases\Privileges\Interfaces\IPrivilegeSetter;
use TheClinicUseCases\Privileges\PrivilegesManagement;

class PrivilegesManagementTest extends TestCase
{
    use MakeAuthenticatable;

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
            ->with('dummyRoleName', $privilegeValue, 'custom');

        $result = $this->instantiate()->createRole($this->authenticated, 'dummyRoleName', $privilegeValue, 'custom', $iDataBaseCreateRole);
        $this->assertNull($result);
    }

    public function testDeleteRole(): void
    {
        $this->authentication->shouldReceive('check')->with($this->authenticated);
        $customRoleName = $this->faker->lexify();

        /** @var IDataBaseDeleteRole|MockInterface $iDataBaseDeleteRole */
        $iDataBaseDeleteRole = Mockery::mock(IDataBaseDeleteRole::class);
        $iDataBaseDeleteRole
            ->shouldReceive('deleteRole')
            ->with($customRoleName);

        $result = $this->instantiate()->deleteRole($this->authenticated, $customRoleName, $iDataBaseDeleteRole);
        $this->assertNull($result);
    }

    public function testGetUserPrivileges(): void
    {
        $this->authentication->shouldReceive('check')->with($this->authenticated);

        $privileges = $this->dsUser->getUserPrivileges();

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

        $privileges = $this->dsUser->getUserPrivileges();

        $array = $this->instantiate()->getSelfPrivileges($this->dsUser);
        $this->assertIsArray($array);
        $this->assertCount(count($privileges), $array);

        foreach ($privileges as $key => $value) {
            $this->assertNotFalse(array_search($key, array_keys($array)));
            $this->assertEquals($value, $array[$key]);
        }
    }

    public function testSetRolePrivilege(): void
    {
        $privilege = "selfAccountRead";
        $roleName = 'custom_role_name';
        $privilegeValues = [$privilege => true];

        /** @var IPrivilegeSetter|MockInterface $ips */
        $ips = Mockery::mock(IPrivilegeSetter::class);
        $ips
            ->shouldReceive('setPrivilege')
            ->with($roleName, $privilegeValues);

        $this->authentication->shouldReceive('check')->with($this->authenticated);

        $instance = $this->instantiate();
        $result = $instance->setRolePrivilege($this->authenticated, $roleName, $privilegeValues, $ips);

        $this->assertNull($result);
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
