<?php

namespace Tests\Visits\Retrieval;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;
use TheClinicDataStructures\DataStructures\Order\Laser\DSLaserOrder;
use TheClinicDataStructures\DataStructures\User\DSAdmin;
use TheClinicDataStructures\DataStructures\User\DSPatient;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicDataStructures\DataStructures\User\ICheckAuthentication;
use TheClinicDataStructures\DataStructures\Visit\Laser\DSLaserVisit;
use TheClinicDataStructures\DataStructures\Visit\Laser\DSLaserVisits;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Privileges\PrivilegesManagement;
use TheClinicUseCases\Visits\Interfaces\IDataBaseRetrieveLaserVisits;
use TheClinicUseCases\Visits\Retrieval\LaserVisitRetrieval;

class LaserVisitRetrievalTest extends TestCase
{
    private Generator $faker;

    private MockInterface|Authentication $authentication;

    private MockInterface|PrivilegesManagement $privilegesManagement;

    private MockInterface|DSUser $authenticated;

    private MockInterface|DSUser $dsUser;

    private MockInterface|IDataBaseRetrieveLaserVisits $db;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();

        $this->authenticated = $this->makeAuthenticatable(true);

        $this->dsUser = $this->makeAuthenticatable(false);

        /** @var Authentication|\Mockery\MockInterface $authentication */
        $this->authentication = Mockery::mock(Authentication::class);
        $this->authentication
            ->shouldReceive('check')
            // 
        ;

        /** @var PrivilegesManagement|\Mockery\MockInterface $privilegesManagement */
        $this->privilegesManagement = Mockery::mock(PrivilegesManagement::class);

        /** @var IDataBaseRetrieveLaserVisits|\Mockery\MockInterface $db */
        $this->db = Mockery::mock(IDataBaseRetrieveLaserVisits::class);
    }

    private function makeAuthenticatable($admin = false): DSUser
    {
        /** @var ICheckAuthentication|\Mockery\MockInterface $icheckAuthentication */
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

    private function instantiate(): LaserVisitRetrieval
    {
        return new LaserVisitRetrieval($this->authentication, $this->privilegesManagement);
    }

    public function testGetVisitsByUserSelf(): void
    {
        $this->privilegesManagement
            ->shouldReceive('checkBool')
            ->with($this->authenticated, 'selfLaserVisitRetrieve')
            // 
        ;

        $this->db
            ->shouldReceive('getVisitsByUser')
            ->with($this->authenticated, 'desc')
            ->andReturn(new DSLaserVisits)
            // 
        ;

        $visits = $this->instantiate()->getVisitsByUser($this->authenticated, $this->authenticated, 'desc', $this->db);
        $this->assertInstanceOf(DSLaserVisits::class, $visits);
    }

    public function testGetVisitsByUser(): void
    {
        $this->privilegesManagement
            ->shouldReceive('checkBool')
            ->with($this->authenticated, 'laserVisitRetrieve')
            // 
        ;

        $this->db
            ->shouldReceive('getVisitsByUser')
            ->with($this->dsUser, 'desc')
            ->andReturn(new DSLaserVisits)
            // 
        ;

        $visits = $this->instantiate()->getVisitsByUser($this->authenticated, $this->dsUser, 'desc', $this->db);
        $this->assertInstanceOf(DSLaserVisits::class, $visits);
    }

    public function testGetVisitsByOrderSelf(): void
    {
        $this->privilegesManagement
            ->shouldReceive('checkBool')
            ->with($this->authenticated, 'selfLaserVisitRetrieve')
            // 
        ;

        /** @var DSLaserOrder|MockInterface $dsLaserOrder */
        $dsLaserOrder = Mockery::mock(DSLaserOrder::class);

        $this->db
            ->shouldReceive('getVisitsByOrder')
            ->with($this->authenticated, $dsLaserOrder, 'desc')
            ->andReturn(new DSLaserVisits)
            // 
        ;

        $visits = $this->instantiate()->getVisitsByOrder($this->authenticated, $this->authenticated, $dsLaserOrder, 'desc', $this->db);
        $this->assertInstanceOf(DSLaserVisits::class, $visits);
    }

    public function testGetVisitsByOrder(): void
    {
        $this->privilegesManagement
            ->shouldReceive('checkBool')
            ->with($this->authenticated, 'laserVisitRetrieve')
            // 
        ;

        /** @var DSLaserOrder|MockInterface $dsLaserOrder */
        $dsLaserOrder = Mockery::mock(DSLaserOrder::class);

        $this->db
            ->shouldReceive('getVisitsByOrder')
            ->with($this->dsUser, $dsLaserOrder, 'desc')
            ->andReturn(new DSLaserVisits)
            // 
        ;

        $visits = $this->instantiate()->getVisitsByOrder($this->authenticated, $this->dsUser, $dsLaserOrder, 'desc', $this->db);
        $this->assertInstanceOf(DSLaserVisits::class, $visits);
    }

    public function testGetVisitsByTimestamp(): void
    {
        $this->privilegesManagement
            ->shouldReceive('checkBool')
            ->with($this->authenticated, 'laserVisitRetrieve')
            // 
        ;

        $operator = '>=';
        $timestamp = $this->faker->numberBetween(500, 1000000);

        $this->db
            ->shouldReceive('getVisitsByTimestamp')
            ->with($operator, $timestamp, 'desc')
            ->andReturn(new DSLaserVisits)
            // 
        ;

        $visits = $this->instantiate()->getVisitsByTimestamp($this->authenticated, $operator, $timestamp, 'desc', $this->db);
        $this->assertInstanceOf(DSLaserVisits::class, $visits);
    }
}
