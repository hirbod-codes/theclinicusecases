<?php

namespace Tests\Accounts;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use PHPUnit\Framework\TestCase;
use TheClinicUseCases\Accounts\AccountsManagement;
use TheClinicUseCases\Accounts\Interfaces\IDataBaseRetrieveAccounts;

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

        $result = (new AccountsManagement)->getAccounts($id, $count, $db);
        $this->assertIsArray($result);
    }
}
