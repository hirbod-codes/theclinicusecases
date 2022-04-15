<?php

namespace Tests\Accounts;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicUseCases\Accounts\AccountsManagement;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Accounts\ICheckForAuthenticatedUsers;
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
        parent::setUp();

        $this->faker = Factory::create();

        $this->user = $this->makeUser();

        /** @var \TheClinicUseCases\Accounts\Authentication|\Mockery\MockInterface $authentication */
        $this->authentication = Mockery::mock(Authentication::class);
        $this->authentication->shouldReceive("check")->with($this->user);
    }

    private function makeUser(): DSUser|MockInterface
    {
        /** @var \TheClinicDataStructures\DataStructures\User\DSUser|\Mockery\MockInterface $user */
        $user = Mockery::mock(DSUser::class);
        $user->shouldReceive('getId')->andReturn($this->faker->numberBetween(1, 1000));
        $user->shouldReceive('getUsername')->andReturn($this->faker->userName());
        return $user;
    }

    public function testGetAccounts(): void
    {
        $ruleName = 'admin';
        $id = $this->faker->numberBetween(1, 1000);
        $count = $this->faker->numberBetween(1, 30);

        /** @var \TheClinicUseCases\Accounts\Interfaces\IDataBaseRetrieveAccounts|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseRetrieveAccounts::class);
        $db->shouldReceive("getAccounts")->with($count, $ruleName, $id)->andReturn([]);

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, "accountsRead");

        $accounts = (new AccountsManagement($this->authentication, $privilegesManagement))->getAccounts($id, $count, $ruleName, $this->user, $db);
        $this->assertIsArray($accounts);
    }

    public function testGetAccount(): void
    {
        $targetUser = $this->makeUser();
        $targetUser->shouldReceive('getUsername')->andReturn($this->faker->userName());
        $targetUserUsername = $targetUser->getUsername();

        /** @var \TheClinicUseCases\Accounts\Interfaces\IDataBaseRetrieveAccounts|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseRetrieveAccounts::class);
        $db->shouldReceive("getAccount")->with($targetUserUsername)->andReturn($targetUser);

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, "accountRead");

        $account = (new AccountsManagement($this->authentication, $privilegesManagement))->getAccount($targetUserUsername, $this->user, $db);
        $this->assertInstanceOf(DSUser::class, $account);

        /** @var \TheClinicUseCases\Accounts\Interfaces\IDataBaseRetrieveAccounts|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseRetrieveAccounts::class);
        $db->shouldReceive("getAccount")->with($this->user->getUsername())->andReturn($this->user);

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, "accountRead");
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, "selfAccountRead");

        $account = (new AccountsManagement($this->authentication, $privilegesManagement))->getAccount($this->user->getUsername(), $this->user, $db);
        $this->assertInstanceOf(DSUser::class, $account);
    }

    public function testCreateAccount(): void
    {
        $newUser = [];

        /** @var \TheClinicUseCases\Accounts\Interfaces\IDataBaseCreateAccount|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseCreateAccount::class);
        $db->shouldReceive("createAccount")->with($newUser)->andReturn($this->makeUser());

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, "accountCreate");

        $account = (new AccountsManagement($this->authentication, $privilegesManagement))->createAccount($newUser, $this->user, $db);
        $this->assertInstanceOf(DSUser::class, $account);
    }

    public function testSignupAccount(): void
    {
        $newUser = [];

        /** @var \TheClinicUseCases\Accounts\Interfaces\IDataBaseCreateAccount|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseCreateAccount::class);
        $db->shouldReceive("createAccount")->with($newUser)->andReturn($this->makeUser());

        /** @var \TheClinicUseCases\Accounts\ICheckForAuthenticatedUsers|\Mockery\MockInterface $iCheckForAuthenticatedUsers */
        $iCheckForAuthenticatedUsers = Mockery::mock(ICheckForAuthenticatedUsers::class);
        $iCheckForAuthenticatedUsers->shouldReceive("checkIfThereIsNoAuthenticated")->andReturn(true);

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);

        $account = (new AccountsManagement($this->authentication, $privilegesManagement))->signupAccount($newUser, $db, $iCheckForAuthenticatedUsers);
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

        /** @var \TheClinicUseCases\Accounts\Interfaces\IDataBaseDeleteAccount|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseDeleteAccount::class);
        $db->shouldReceive("deleteAccount")->with($this->user);

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, "selfAccountDelete");

        $account = (new AccountsManagement($this->authentication, $privilegesManagement))->deleteAccount($this->user, $this->user, $db);
        $this->assertNull($account);
    }

    public function testmassUpdateAccount(): void
    {
        $attribute = 'firstname';
        $input = [$attribute => 'value'];
        $targetUser = $this->makeUser();

        /** @var \TheClinicUseCases\Accounts\Interfaces\IDataBaseUpdateAccount|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseUpdateAccount::class);
        $db->shouldReceive("massUpdateAccount")->with($input, $targetUser)->andReturn($this->makeUser());

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, "accountUpdate" . ucfirst($attribute));

        $account = (new AccountsManagement($this->authentication, $privilegesManagement))->massUpdateAccount($input, $targetUser, $this->user, $db);
        $this->assertInstanceOf(DSUser::class, $account);

        /** @var \TheClinicUseCases\Accounts\Interfaces\IDataBaseUpdateAccount|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseUpdateAccount::class);
        $db->shouldReceive("massUpdateAccount")->with($input, $this->user)->andReturn($this->makeUser());

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, "selfAccountUpdate" . ucfirst($attribute));

        $account = (new AccountsManagement($this->authentication, $privilegesManagement))->massUpdateAccount($input, $this->user, $this->user, $db);
        $this->assertInstanceOf(DSUser::class, $account);
    }

    public function testupdateAccount(): void
    {
        $targetUser = $this->makeUser();
        $attribute = $this->faker->lexify();
        $newValue = true;

        /** @var \TheClinicUseCases\Accounts\Interfaces\IDataBaseUpdateAccount|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseUpdateAccount::class);
        $db->shouldReceive("updateAccount")->with($attribute, $newValue, $targetUser)->andReturn($this->makeUser());

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, "accountUpdate" . ucfirst($attribute));

        $account = (new AccountsManagement($this->authentication, $privilegesManagement))->updateAccount($attribute, $newValue, $targetUser, $this->user, $db);
        $this->assertInstanceOf(DSUser::class, $account);

        /** @var \TheClinicUseCases\Accounts\Interfaces\IDataBaseUpdateAccount|\Mockery\MockInterface $db */
        $db = Mockery::mock(IDataBaseUpdateAccount::class);
        $db->shouldReceive("updateAccount")->with($attribute, $newValue, $this->user)->andReturn($this->makeUser());

        /** @var \TheClinicUseCases\Privileges\PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $privilegesManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegesManagement->shouldReceive("checkBool")->with($this->user, "selfAccountUpdate" . ucfirst($attribute));

        $account = (new AccountsManagement($this->authentication, $privilegesManagement))->updateAccount($attribute, $newValue, $this->user, $this->user, $db);
        $this->assertInstanceOf(DSUser::class, $account);
    }
}
