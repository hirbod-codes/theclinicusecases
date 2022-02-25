<?php

namespace Tests\Accounts;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use Tests\TestCase;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicUseCases\Accounts\AccountsManagement;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Accounts\Interfaces\IDataBaseCreateAccount;
use TheClinicUseCases\Accounts\Interfaces\IDataBaseDeleteAccount;
use TheClinicUseCases\Accounts\Interfaces\IDataBaseRetrieveAccounts;
use TheClinicUseCases\Accounts\Interfaces\IDataBaseUpdateAccount;
use TheClinicUseCases\Privileges\PrivilegesManagement;

class AccountsManagementTest extends TestCase
{
    private Generator $faker;

    private DSUser|\Mockery\MockInterface $user;

    private Authentication|\Mockery\MockInterface $authentication;

    protected function setUp(): void
    {
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

    public function testGetAccounts(): void
    {
        $id = $this->faker->numberBetween(1, 1000);
        $count = $this->faker->numberBetween(1, 30);

        /** @var \TheClinicUseCases\Accounts\Interfaces\IDataBaseRetrieveAccounts|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseRetrieveAccounts::class);
        $db->shouldReceive("getAccounts")->with($id, $count)->andReturn([]);

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, "accountsRead");

        $accounts = (new AccountsManagement($this->authentication, $privilegesManagement))->getAccounts($id, $count, $this->user, $db);
        $this->assertIsArray($accounts);
    }

    public function testGetSelfAccounts(): void
    {
        $id = $this->faker->numberBetween(1, 1000);
        $this->user->shouldReceive("getId")->andReturn($id);

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, "selfAccountRead");

        /** @var \TheClinicUseCases\Accounts\Interfaces\IDataBaseRetrieveAccounts|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseRetrieveAccounts::class);
        $db->shouldReceive("getAccount")->with($this->user->getId())->andReturn($this->user);

        $account = (new AccountsManagement($this->authentication, $privilegesManagement))->getSelfAccounts($this->user, $db);
        $this->assertInstanceOf(DSUser::class, $account);
    }

    public function testCreateAccount(): void
    {
        $input = [];

        /** @var \TheClinicUseCases\Accounts\Interfaces\IDataBaseCreateAccount|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseCreateAccount::class);
        $db->shouldReceive("createAccount")->with($input)->andReturn($this->makeUser());

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, "accountCreate");

        $account = (new AccountsManagement($this->authentication, $privilegesManagement))->createAccount($input, $this->user, $db);
        $this->assertInstanceOf(DSUser::class, $account);
    }

    public function testDeleteAccount(): void
    {
        $targetUser = $this->makeUser();
        /** @var \TheClinicUseCases\Accounts\Interfaces\IDataBaseDeleteAccount|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseDeleteAccount::class);
        $db->shouldReceive("deleteAccount")->with($targetUser);

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, "accountDelete");

        $account = (new AccountsManagement($this->authentication, $privilegesManagement))->deleteAccount($targetUser, $this->user, $db);
        $this->assertNull($account);
    }

    public function testDeleteSelfAccount(): void
    {
        /** @var \TheClinicUseCases\Accounts\Interfaces\IDataBaseDeleteAccount|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseDeleteAccount::class);
        $db->shouldReceive("deleteAccount")->with($this->user);

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, "selfAccountDelete");

        $account = (new AccountsManagement($this->authentication, $privilegesManagement))->deleteSelfAccount($this->user, $db);
        $this->assertNull($account);
    }

    public function testUpdateAccount(): void
    {
        $input = [];

        $targetUser = $this->makeUser();

        /** @var \TheClinicUseCases\Accounts\Interfaces\IDataBaseUpdateAccount|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseUpdateAccount::class);
        $db->shouldReceive("updateAccount")->with($input, $targetUser)->andReturn($this->makeUser());

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, "accountUpdate");

        $account = (new AccountsManagement($this->authentication, $privilegesManagement))->updateAccount($input, $targetUser, $this->user, $db);
        $this->assertInstanceOf(DSUser::class, $account);
    }
}
