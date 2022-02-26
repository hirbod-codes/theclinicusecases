<?php

namespace TheClinicUseCases\Orders\Creation;

use TheClinic\Order\ICalculateLaserOrder;
use TheClinic\Order\ICalculateRegularOrder;
use TheClinic\Order\Laser\Calculations\PriceCalculator;
use TheClinic\Order\Laser\Calculations\TimeConsumptionCalculator;
use TheClinic\Order\Laser\ILaserPriceCalculator;
use TheClinic\Order\Laser\ILaserTimeConsumptionCalculator;
use TheClinic\Order\Laser\LaserOrder;
use TheClinic\Order\Regular\RegularOrder;
use TheClinicUseCases\Accounts\Authentication;
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
}