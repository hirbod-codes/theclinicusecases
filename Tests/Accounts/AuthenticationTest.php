<?php

namespace Tests\Accounts;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use Tests\TestCase;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Exceptions\Accounts\UserIsNotAuthenticated;

class AuthenticationTest extends TestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        $this->faker = Factory::create();
    }

    public function testCheck(): void
    {
        /** @var \TheClinicDataStructures\DataStructures\User\DSUser|\Mockery\MockInterface $dsUser */
        $dsUser = Mockery::mock(DSUser::class);
        $dsUser->shouldReceive("isAuthenticated")->andReturn(true);

        $result = (new Authentication)->check($dsUser);
        $this->assertNull($result);

        /** @var \TheClinicDataStructures\DataStructures\User\DSUser|\Mockery\MockInterface $dsUser */
        $dsUser = Mockery::mock(DSUser::class);
        $dsUser->shouldReceive("isAuthenticated")->andReturn(false);

        try {
            (new Authentication)->check($dsUser);
            throw new \RuntimeException("Failure!!!", 500);
        } catch (UserIsNotAuthenticated $th) {
        }
    }
}
