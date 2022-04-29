<?php

namespace Tests\visits\Creation;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;
use Tests\Traits\MakeAuthenticatable;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicDataStructures\DataStructures\Visit\Regular\DSRegularVisit;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Privileges\PrivilegesManagement;
use TheClinicUseCases\Visits\Deletion\RegularVisitDeletion;
use TheClinicUseCases\Visits\Interfaces\IDataBaseDeleteRegularVisit;

class RegularVisitDeletionTest extends TestCase
{
    use MakeAuthenticatable;

    private Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();
    }

    public function testDelete(): void
    {
        $user = $this->makeAuthenticatable(true);

        $targetUser = $this->makeAuthenticatable();

        $this->deleteTester($user, $user, "selfRegularVisitDelete");
        $this->deleteTester($user, $targetUser, "regularVisitDelete");
    }

    public function deleteTester(DSUser $user, DSUser $targetUser, string $privilege): void
    {
        /** @var Authentication|MockInterface $authentication */
        $authentication = Mockery::mock(Authentication::class);
        $authentication->shouldReceive("check")->with($user);

        /** @var PrivilegesManagement|MockInterface $privilegeManagement */
        $privilegeManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegeManagement->shouldReceive("checkBool")->with($user, $privilege);

        /** @var DSRegularVisit|MockInterface $dsRegularVisit */
        $dsRegularVisit = Mockery::mock(DSRegularVisit::class);

        /** @var IDataBaseDeleteRegularVisit|MockInterface $db */
        $db = Mockery::mock(IDataBaseDeleteRegularVisit::class);
        $db->shouldReceive("deleteRegularVisit")->with($dsRegularVisit, $targetUser);

        $result = (new RegularVisitDeletion($authentication, $privilegeManagement))->delete($dsRegularVisit, $targetUser, $user, $db);
        $this->assertNull($result);
    }
}
