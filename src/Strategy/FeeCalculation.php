<?php

namespace Ivan\Strategy;

use Ivan\ValueObject\OperationData;
use Money\Money;

interface FeeCalculation
{
    public function calculate(OperationData $operationData): Money;
}
