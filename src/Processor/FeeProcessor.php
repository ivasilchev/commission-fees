<?php

namespace Ivan\Processor;

use Ivan\ValueObject\CalculationResult;
use Ivan\ValueObject\OperationData;

interface FeeProcessor
{
    public function process(OperationData $operationData): CalculationResult;
}
