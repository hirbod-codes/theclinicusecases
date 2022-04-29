<?php

namespace Tests\visits\Creation;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;
use Tests\Traits\MakeAuthenticatable;
use TheClinic\Visit\IFindVisit;
use TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrder;
use TheClinicDataStructures\DataStructures\User\DSAdmin;
use TheClinicDataStructures\DataStructures\User\DSPatient;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicDataStructures\DataStructures\User\ICheckAuthentication;
use TheClinicDataStructures\DataStructures\Visit\Regular\DSRegularVisit;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Privileges\PrivilegesManagement;
use TheClinicUseCases\Visits\Creation\RegularVisitCreation;
use TheClinicUseCases\Visits\Interfaces\IDataBaseCreateRegularVisit;

class RegularVisitCreationTest extends TestCase
{
    use MakeAuthenticatable;

    private Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();
    }

    public function testCreate(): void
    {
        $user = $this->makeAuthenticatable(true);

        $targetUser = $this->makeAuthenticatable();

        $this->createTester($user, $user, "selfRegularVisitCreate");
        $this->createTester($user, $targetUser, "regularVisitCreate");
    }

    public function createTester(DSUser $user, DSUser $targetUser, string $privilege): void
    {
        $timestamp = $this->faker->numerify("#########");

        /** @var Authentication|MockInterface $authentication */
        $authentication = Mockery::mock(Authentication::class);
        $authentication->shouldReceive("check")->with($user);
        /** @var PrivilegesManagement|MockInterface $privilegeManagement */
        $privilegeManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegeManagement->shouldReceive("checkBool")->with($user, $privilege);

        /** @var DSRegularOrder|MockInterface $dsRegularOrder */
        $dsRegularOrder = Mockery::mock(DSRegularOrder::class);

        /** @var DSRegularVisit|MockInterface $dsRegularVisit */
        $dsRegularVisit = Mockery::mock(DSRegularVisit::class);

        /** @var IFindVisit|MockInterface $iFindVisit */
        $iFindVisit = Mockery::mock(IFindVisit::class);

        /** @var IDataBaseCreateRegularVisit|MockInterface $db */
        $db = Mockery::mock(IDataBaseCreateRegularVisit::class);
        $db
            ->shouldReceive("createRegularVisit")
            ->with($dsRegularOrder, $targetUser, $iFindVisit)
            ->andReturn($dsRegularVisit);

        $createdVisit = (new RegularVisitCreation($authentication, $privilegeManagement))
            ->create($dsRegularOrder, $targetUser, $user, $db, $iFindVisit);
        $this->assertInstanceOf(DSRegularVisit::class, $createdVisit);
    }
}
