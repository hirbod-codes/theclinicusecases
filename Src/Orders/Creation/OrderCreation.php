<?php

namespace TheClinicUseCases\Orders\Creation;

use TheClinic\Exceptions\Order\InvalidGenderException;
use TheClinic\Exceptions\Order\NoPackageOrPartException;
use TheClinic\Order\ICalculateLaserOrder;
use TheClinic\Order\ICalculateRegularOrder;
use TheClinic\Order\Laser\Calculations\PriceCalculator;
use TheClinic\Order\Laser\Calculations\TimeConsumptionCalculator;
use TheClinic\Order\Laser\ILaserPriceCalculator;
use TheClinic\Order\Laser\ILaserTimeConsumptionCalculator;
use TheClinic\Order\Laser\LaserOrder;
use TheClinic\Order\Regular\RegularOrder;
use TheClinicDataStructures\DataStructures\Order\DSPackages;
use TheClinicDataStructures\DataStructures\Order\DSParts;
use TheClinicDataStructures\DataStructures\Order\Laser\DSLaserOrder;
use TheClinicDataStructures\DataStructures\Order\Regular\DSRegularOrder;
use TheClinicDataStructures\DataStructures\User\DSUser;
use TheClinicUseCases\Accounts\Authentication;
use TheClinicUseCases\Orders\Interfaces\IDataBaseCreateLaserOrder;
use TheClinicUseCases\Orders\Interfaces\IDataBaseCreateRegularOrder;
use TheClinicUseCases\Privileges\PrivilegesManagement;

class OrderCreation
{
    private Authentication $authentication;

    private PrivilegesManagement $privilegesManagement;

    private ICalculateRegularOrder $iCalculateRegularOrder;

    private ICalculateLaserOrder $iCalculateLaserOrder;

    private ILaserPriceCalculator $iLaserPriceCalculator;

    private ILaserTimeConsumptionCalculator $iLaserTimeConsumptionCalculator;

    public function __construct(
        Authentication $authentication,
        PrivilegesManagement $privilegesManagement,
        ICalculateRegularOrder $iCalculateRegularOrder = null,
        ICalculateLaserOrder $iCalculateLaserOrder = null,
        ILaserPriceCalculator $iLaserPriceCalculator = null,
        ILaserTimeConsumptionCalculator $iLaserTimeConsumptionCalculator = null
    ) {
        $this->iCalculateRegularOrder = $iCalculateRegularOrder ?: new RegularOrder;

        $this->iLaserPriceCalculator = $iLaserPriceCalculator ?: new PriceCalculator;
        $this->iLaserTimeConsumptionCalculator = $iLaserTimeConsumptionCalculator ?: new TimeConsumptionCalculator;
        $this->iCalculateLaserOrder = $iCalculateLaserOrder ?: new LaserOrder;

        $this->authentication = $authentication;
        $this->privilegesManagement = $privilegesManagement;
    }

    public function createRegularOrder(DSUser $targetUser, DSUser $user, IDataBaseCreateRegularOrder $db): DSRegularOrder
    {
        if ($targetUser->getId() === $user->getId()) {
            $privilege = "selfRegularOrderCreate";
        } else {
            $privilege = "regularOrderCreate";
        }

        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, $privilege);

        return $db->createRegularOrder($targetUser, $this->iCalculateRegularOrder->calculatePrice(), $this->iCalculateRegularOrder->calculateTimeConsumption());
    }

    public function createLaserOrder(DSUser $targetUser, DSUser $user, ?DSParts $parts = null, ?DSPackages $packages = null, IDataBaseCreateLaserOrder $db): DSLaserOrder
    {
        if ($targetUser->getId() === $user->getId()) {
            $privilege = "selfLaserOrderCreate";
        } else {
            $privilege = "laserOrderCreate";
        }

        $this->authentication->check($user);
        $this->privilegesManagement->checkBool($user, $privilege);

        if (($parts !== null && $targetUser->getGender() !== $parts->getGender()) || ($packages !== null && $targetUser->getGender() !== $packages->getGender())) {
            throw new InvalidGenderException("User, parts and packages must have the same gender.", 500);
        } elseif ($parts === null && $packages === null) {
            throw new NoPackageOrPartException("One of the parts or packages must exist.", 500);
        }

        return $db->createLaserOrder(
            $targetUser,
            $parts,
            $packages,
            $this->iCalculateLaserOrder->calculatePrice($parts, $packages, $this->iLaserPriceCalculator),
            $this->iCalculateLaserOrder->calculateTimeConsumption($parts, $packages, $this->iLaserTimeConsumptionCalculator),
            $this->iCalculateLaserOrder->calculatePriceWithoutDiscount($parts, $packages, $this->iLaserPriceCalculator)
        );
    }
}
