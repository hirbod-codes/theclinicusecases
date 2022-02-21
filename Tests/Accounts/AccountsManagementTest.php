<?php

namespace Tests\Accounts;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use PHPUnit\Framework\TestCase;
use TheClinic\DataStructures\User\DSUser;
use TheClinic\DataStructures\User\IUserRule;
use TheClinicUseCases\Accounts\AccountsManagement;
use TheClinicUseCases\Accounts\Interfaces\IDataBaseCreateAccount;
use TheClinicUseCases\Accounts\Interfaces\IDataBaseRetrieveAccounts;
use TheClinicUseCases\Exceptions\PrivilegeNotFound;
use TheClinicUseCases\Exceptions\Accounts\UserIsNotAuthenticated;
use TheClinicUseCases\Exceptions\Accounts\UserIsNotAuthorized;

class AccountsManagementTest extends TestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        $this->faker = Factory::create();
    }

    public function testGetAccounts(): void
    {
        $id = $this->faker->numberBetween(1, 1000);
        $count = $this->faker->numberBetween(1, 30);

        /** @var \TheClinicUseCases\Accounts\Interfaces\IDataBaseRetrieveAccounts|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseRetrieveAccounts::class);
        $db->shouldReceive("getAccounts")->with($id, $count)->andReturn([]);

        /** @var \TheClinic\DataStructures\User\IUserRule|\Mockery\MockInterface $rule */
        $rule = Mockery::mock(IUserRule::class);
        $rule->shouldReceive("privilegeExists")->with("accountsRead")->andReturn(true);
        $rule->shouldReceive("getPrivilegeValue")->with("accountsRead")->andReturn(true);

        /** @var \TheClinic\DataStructures\User\DSUser|\Mockery\MockInterface $user */
        $user = Mockery::mock(DSUser::class);
        $user->shouldReceive("isAuthenticated")->andReturn(true);
        $user->shouldReceive("getRole")->andReturn($rule);

        $accounts = (new AccountsManagement)->getAccounts($id, $count, $user, $db);
        $this->assertIsArray($accounts);

        try {
            /** @var \TheClinic\DataStructures\User\IUserRule|\Mockery\MockInterface $rule */
            $rule = Mockery::mock(IUserRule::class);
            $rule->shouldReceive("privilegeExists")->with("accountsRead")->andReturn(false);
            $rule->shouldReceive("getPrivilegeValue")->with("accountsRead")->andReturn(true);

            /** @var \TheClinic\DataStructures\User\DSUser|\Mockery\MockInterface $user */
            $user = Mockery::mock(DSUser::class);
            $user->shouldReceive("isAuthenticated")->andReturn(true);
            $user->shouldReceive("getRole")->andReturn($rule);

            (new AccountsManagement)->getAccounts($id, $count, $user, $db);
            throw new \RuntimeException("Failure!!!", 500);
        } catch (PrivilegeNotFound $th) {
            $this->assertEquals("There is no such privilege.", $th->getMessage());
        }

        try {
            /** @var \TheClinic\DataStructures\User\IUserRule|\Mockery\MockInterface $rule */
            $rule = Mockery::mock(IUserRule::class);
            $rule->shouldReceive("privilegeExists")->with("accountsRead")->andReturn(true);
            $rule->shouldReceive("getPrivilegeValue")->with("accountsRead")->andReturn(false);

            /** @var \TheClinic\DataStructures\User\DSUser|\Mockery\MockInterface $user */
            $user = Mockery::mock(DSUser::class);
            $user->shouldReceive("isAuthenticated")->andReturn(true);
            $user->shouldReceive("getRole")->andReturn($rule);

            (new AccountsManagement)->getAccounts($id, $count, $user, $db);
            throw new \RuntimeException("Failure!!!", 500);
        } catch (UserIsNotAuthorized $th) {
            $this->assertEquals("The current authenticated user is not authorized for this action.", $th->getMessage());
        }

        try {
            /** @var \TheClinic\DataStructures\User\IUserRule|\Mockery\MockInterface $rule */
            $rule = Mockery::mock(IUserRule::class);
            $rule->shouldReceive("privilegeExists")->with("accountsRead")->andReturn(true);
            $rule->shouldReceive("getPrivilegeValue")->with("accountsRead")->andReturn(true);

            /** @var \TheClinic\DataStructures\User\DSUser|\Mockery\MockInterface $user */
            $user = Mockery::mock(DSUser::class);
            $user->shouldReceive("isAuthenticated")->andReturn(false);
            $user->shouldReceive("getRole")->andReturn($rule);

            (new AccountsManagement)->getAccounts($id, $count, $user, $db);
            throw new \RuntimeException("Failure!!!", 500);
        } catch (UserIsNotAuthenticated $th) {
            $this->assertEquals("The current authenticated user is not authenticated.", $th->getMessage());
        }
    }

    public function testCreateAccount(): void
    {
        /** @var \TheClinicUseCases\Accounts\Interfaces\IDataBaseCreateAccount|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseCreateAccount::class);
        $db->shouldReceive("createAccount")->with([]);

        /** @var \TheClinic\DataStructures\User\IUserRule|\Mockery\MockInterface $rule */
        $rule = Mockery::mock(IUserRule::class);
        $rule->shouldReceive("privilegeExists")->with("accountCreate")->andReturn(true);
        $rule->shouldReceive("getPrivilegeValue")->with("accountCreate")->andReturn(true);

        /** @var \TheClinic\DataStructures\User\DSUser|\Mockery\MockInterface $user */
        $user = Mockery::mock(DSUser::class);
        $user->shouldReceive("isAuthenticated")->andReturn(true);
        $user->shouldReceive("getRole")->andReturn($rule);

        (new AccountsManagement)->createAccount([], $user, $db);

        try {
            /** @var \TheClinic\DataStructures\User\IUserRule|\Mockery\MockInterface $rule */
            $rule = Mockery::mock(IUserRule::class);
            $rule->shouldReceive("privilegeExists")->with("accountCreate")->andReturn(false);
            $rule->shouldReceive("getPrivilegeValue")->with("accountCreate")->andReturn(true);

            /** @var \TheClinic\DataStructures\User\DSUser|\Mockery\MockInterface $user */
            $user = Mockery::mock(DSUser::class);
            $user->shouldReceive("isAuthenticated")->andReturn(true);
            $user->shouldReceive("getRole")->andReturn($rule);

            (new AccountsManagement)->createAccount([], $user, $db);
            throw new \RuntimeException("Failure!!!", 500);
        } catch (PrivilegeNotFound $th) {
            $this->assertEquals("There is no such privilege.", $th->getMessage());
        }

        try {
            /** @var \TheClinic\DataStructures\User\IUserRule|\Mockery\MockInterface $rule */
            $rule = Mockery::mock(IUserRule::class);
            $rule->shouldReceive("privilegeExists")->with("accountCreate")->andReturn(true);
            $rule->shouldReceive("getPrivilegeValue")->with("accountCreate")->andReturn(false);

            /** @var \TheClinic\DataStructures\User\DSUser|\Mockery\MockInterface $user */
            $user = Mockery::mock(DSUser::class);
            $user->shouldReceive("isAuthenticated")->andReturn(true);
            $user->shouldReceive("getRole")->andReturn($rule);

            (new AccountsManagement)->createAccount([], $user, $db);
            throw new \RuntimeException("Failure!!!", 500);
        } catch (UserIsNotAuthorized $th) {
            $this->assertEquals("The current authenticated user is not authorized for this action.", $th->getMessage());
        }

        try {
            /** @var \TheClinic\DataStructures\User\IUserRule|\Mockery\MockInterface $rule */
            $rule = Mockery::mock(IUserRule::class);
            $rule->shouldReceive("privilegeExists")->with("accountCreate")->andReturn(true);
            $rule->shouldReceive("getPrivilegeValue")->with("accountCreate")->andReturn(true);

            /** @var \TheClinic\DataStructures\User\DSUser|\Mockery\MockInterface $user */
            $user = Mockery::mock(DSUser::class);
            $user->shouldReceive("isAuthenticated")->andReturn(false);
            $user->shouldReceive("getRole")->andReturn($rule);

            (new AccountsManagement)->createAccount([], $user, $db);
            throw new \RuntimeException("Failure!!!", 500);
        } catch (UserIsNotAuthenticated $th) {
            $this->assertEquals("The current authenticated user is not authenticated.", $th->getMessage());
        }
    }
}
