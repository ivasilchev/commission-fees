<?php

namespace Ivan\Strategy;

use Ivan\ValueObject\OperationData;

interface Resolver
{
    public function resolveFor(OperationData $operationData): FeeCalculation;
}
