<?php

namespace Tests\Visits\Retrieval;

use Faker\Factory;
use Faker\Generator;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;
use TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrder;
use TheClinicDataStructures\DataStructures\User\DSAdmin;
use TheClinicDataStructures\DataStructures\User\DSPatient;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicDataStructures\DataStructures\User\ICheckAuthentication;
use TheClinicDataStructures\DataStructures\Visit\Regular\DSRegularVisit;
use TheClinicDataStructures\DataStructures\Visit\Regular\DSRegularVisits;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Privileges\PrivilegesManagement;
use TheClinicUseCases\Visits\Interfaces\IDataBaseRetrieveRegularVisits;
use TheClinicUseCases\Visits\Retrieval\RegularVisitRetrieval;

class RegularVisitRetrievalTest extends TestCase
{
    private Generator $faker;

    private MockInterface|Authentication $authentication;

    private MockInterface|PrivilegesManagement $privilegesManagement;

    private MockInterface|DSUser $authenticated;

    private MockInterface|DSUser $dsUser;

    private MockInterface|IDataBaseRetrieveRegularVisits $db;

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

        /** @var IDataBaseRetrieveRegularVisits|\Mockery\MockInterface $db */
        $this->db = Mockery::mock(IDataBaseRetrieveRegularVisits::class);
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

    private function instantiate(): RegularVisitRetrieval
    {
        return new RegularVisitRetrieval($this->authentication, $this->privilegesManagement);
    }

    public function testGetVisitsByUserSelf(): void
    {
        $this->privilegesManagement
            ->shouldReceive('checkBool')
            ->with($this->authenticated, 'selfRegularVisitRetrieve')
            // 
        ;

        $this->db
            ->shouldReceive('getVisitsByUser')
            ->with($this->authenticated, 'desc')
            ->andReturn(new DSRegularVisits)
            // 
        ;

        $visits = $this->instantiate()->getVisitsByUser($this->authenticated, $this->authenticated, 'desc', $this->db);
        $this->assertInstanceOf(DSRegularVisits::class, $visits);
    }

    public function testGetVisitsByUser(): void
    {
        $this->privilegesManagement
            ->shouldReceive('checkBool')
            ->with($this->authenticated, 'regularVisitRetrieve')
            // 
        ;

        $this->db
            ->shouldReceive('getVisitsByUser')
            ->with($this->dsUser, 'desc')
            ->andReturn(new DSRegularVisits)
            // 
        ;

        $visits = $this->instantiate()->getVisitsByUser($this->authenticated, $this->dsUser, 'desc', $this->db);
        $this->assertInstanceOf(DSRegularVisits::class, $visits);
    }

    public function testGetVisitsByOrderSelf(): void
    {
        $this->privilegesManagement
            ->shouldReceive('checkBool')
            ->with($this->authenticated, 'selfRegularVisitRetrieve')
            // 
        ;

        /** @var DSRegularOrder|MockInterface $dsRegularOrder */
        $dsRegularOrder = Mockery::mock(DSRegularOrder::class);

        $this->db
            ->shouldReceive('getVisitsByOrder')
            ->with($this->authenticated, $dsRegularOrder, 'desc')
            ->andReturn(new DSRegularVisits)
            // 
        ;

        $visits = $this->instantiate()->getVisitsByOrder($this->authenticated, $this->authenticated, $dsRegularOrder, 'desc', $this->db);
        $this->assertInstanceOf(DSRegularVisits::class, $visits);
    }

    public function testGetVisitsByOrder(): void
    {
        $this->privilegesManagement
            ->shouldReceive('checkBool')
            ->with($this->authenticated, 'regularVisitRetrieve')
            // 
        ;

        /** @var DSRegularOrder|MockInterface $dsRegularOrder */
        $dsRegularOrder = Mockery::mock(DSRegularOrder::class);

        $this->db
            ->shouldReceive('getVisitsByOrder')
            ->with($this->dsUser, $dsRegularOrder, 'desc')
            ->andReturn(new DSRegularVisits)
            // 
        ;

        $visits = $this->instantiate()->getVisitsByOrder($this->authenticated, $this->dsUser, $dsRegularOrder, 'desc', $this->db);
        $this->assertInstanceOf(DSRegularVisits::class, $visits);
    }

    public function testGetVisitsByTimestampSelf(): void
    {
        $this->privilegesManagement
            ->shouldReceive('checkBool')
            ->with($this->authenticated, 'selfRegularVisitRetrieve')
            // 
        ;

        $timestamp = $this->faker->numberBetween(500, 1000000);

        $this->db
            ->shouldReceive('getVisitsByTimestamp')
            ->with($this->authenticated, $timestamp, 'desc')
            ->andReturn(new DSRegularVisits)
            // 
        ;

        $visits = $this->instantiate()->getVisitsByTimestamp($this->authenticated, $this->authenticated, $timestamp, 'desc', $this->db);
        $this->assertInstanceOf(DSRegularVisits::class, $visits);
    }

    public function testGetVisitsByTimestamp(): void
    {
        $this->privilegesManagement
            ->shouldReceive('checkBool')
            ->with($this->authenticated, 'regularVisitRetrieve')
            // 
        ;

        $timestamp = $this->faker->numberBetween(500, 1000000);

        $this->db
            ->shouldReceive('getVisitsByTimestamp')
            ->with($this->dsUser, $timestamp, 'desc')
            ->andReturn(new DSRegularVisits)
            // 
        ;

        $visits = $this->instantiate()->getVisitsByTimestamp($this->authenticated, $this->dsUser, $timestamp, 'desc', $this->db);
        $this->assertInstanceOf(DSRegularVisits::class, $visits);
    }

    public function testGetVisitByTimestampSelf(): void
    {
        $this->privilegesManagement
            ->shouldReceive('checkBool')
            ->with($this->authenticated, 'selfRegularVisitRetrieve')
            // 
        ;

        $timestamp = $this->faker->numberBetween(500, 1000000);

        $dsLaserVisit = Mockery::mock(DSRegularVisit::class);

        $this->db
            ->shouldReceive('getVisitByTimestamp')
            ->with($this->authenticated, $timestamp)
            ->andReturn($dsLaserVisit)
            // 
        ;

        $visits = $this->instantiate()->getVisitByTimestamp($this->authenticated, $this->authenticated, $timestamp, $this->db);
        $this->assertInstanceOf(DSRegularVisit::class, $visits);
    }

    public function testGetVisitByTimestamp(): void
    {
        $this->privilegesManagement
            ->shouldReceive('checkBool')
            ->with($this->authenticated, 'regularVisitRetrieve')
            // 
        ;

        $timestamp = $this->faker->numberBetween(500, 1000000);

        $dsLaserVisit = Mockery::mock(DSRegularVisit::class);

        $this->db
            ->shouldReceive('getVisitByTimestamp')
            ->with($this->dsUser, $timestamp)
            ->andReturn($dsLaserVisit)
            // 
        ;

        $visits = $this->instantiate()->getVisitByTimestamp($this->authenticated, $this->dsUser, $timestamp, $this->db);
        $this->assertInstanceOf(DSRegularVisit::class, $visits);
    }
}
