<?php

namespace Tests\visits\Creation;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;
use Tests\Traits\MakeAuthenticatable;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicDataStructures\DataStructures\Visit\Laser\DSLaserVisit;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Privileges\PrivilegesManagement;
use TheClinicUseCases\Visits\Deletion\LaserVisitDeletion;
use TheClinicUseCases\Visits\Interfaces\IDataBaseDeleteLaserVisit;

class LaserVisitDeletionTest extends TestCase
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

        $this->deleteTester($user, $user, "selfLaserVisitDelete");
        $this->deleteTester($user, $targetUser, "laserVisitDelete");
    }

    public function deleteTester(DSUser $user, DSUser $targetUser, string $privilege): void
    {
        /** @var Authentication|MockInterface $authentication */
        $authentication = Mockery::mock(Authentication::class);
        $authentication->shouldReceive("check")->with($user);

        /** @var PrivilegesManagement|MockInterface $privilegeManagement */
        $privilegeManagement = Mockery::mock(PrivilegesManagement::class);
        $privilegeManagement->shouldReceive("checkBool")->with($user, $privilege);

        /** @var DSLaserVisit|MockInterface $dsLaserVisit */
        $dsLaserVisit = Mockery::mock(DSLaserVisit::class);

        /** @var IDataBaseDeleteLaserVisit|MockInterface $db */
        $db = Mockery::mock(IDataBaseDeleteLaserVisit::class);
        $db->shouldReceive("deleteLaserVisit")->with($dsLaserVisit, $targetUser);

        $result = (new LaserVisitDeletion($authentication, $privilegeManagement))->delete($dsLaserVisit, $targetUser, $user, $db);
        $this->assertNull($result);
    }
}
