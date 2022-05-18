<?php

namespace Tests\Traits;

use Mockery;
use TheClinicDataStructures\DataStructures\User\DSAdmin;
use TheClinicDataStructures\DataStructures\User\DSPatient;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicDataStructures\DataStructures\User\ICheckAuthentication;
use TheClinicDataStructures\DataStructures\User\Interfaces\IPrivilege;

trait MakeAuthenticatable
{
    private function makeAuthenticatable($admin = false): DSUser
    {
        /** @var IPrivilege|MockInterface $iPrivilege */
        $iPrivilege = Mockery::mock(IPrivilege::class);

        /** @var ICheckAuthentication|MockInterface $iCheckAuthentication */
        $iCheckAuthentication = Mockery::mock(ICheckAuthentication::class);

        $id = $this->faker->numberBetween(1, 1000);
        $firstname = $this->faker->firstName();
        $lastname = $this->faker->lastName();
        $username = $this->faker->userName();
        $gender = $this->faker->randomElement(["Male", "Female"]);
        $email = $this->faker->safeEmail();
        $emailVerifiedAt = new \DateTime;
        $phonenumber = $this->faker->phoneNumber();
        $phonenumberVerifiedAt = new \DateTime;
        $orders = null;
        $createdAt = new \DateTime;
        $updatedAt = new \DateTime;
        $age = $this->faker->numberBetween(16, 70);
        $state = $this->faker->state();
        $city = $this->faker->city();
        $address = $this->faker->address();
        $laserGrade = $this->faker->numerify() . '/' . $this->faker->numerify();

        $constructArgs['iPrivilege'] = $iPrivilege;
        $constructArgs['iCheckAuthentication'] = $iCheckAuthentication;
        $constructArgs['orders'] = $orders;

        if ($admin === true) {
            foreach (DSAdmin::getAttributes() as $attribute => $types) {
                $constructArgs[$attribute] = $$attribute;
            }

            return new DSAdmin(...$constructArgs);
        } else {
            foreach (DSPatient::getAttributes() as $attribute => $types) {
                $constructArgs[$attribute] = $$attribute;
            }

            return new DSPatient(...$constructArgs);
        }
    }
}
