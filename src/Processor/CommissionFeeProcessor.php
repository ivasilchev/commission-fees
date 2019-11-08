<?php

namespace Ivan\Processor;

use Ivan\Strategy\Resolver;
use Ivan\ValueObject\OperationData;
use Ivan\ValueObject\CalculationResult;

class CommissionFeeProcessor implements FeeProcessor
{
    private $strategyResolver;

    public function __construct(Resolver $strategyResolver)
    {
        $this->strategyResolver = $strategyResolver;
    }

    public function process(OperationData $operationData): CalculationResult
    {
        $strategy = $this->strategyResolver->resolveFor($operationData);
        return new CalculationResult($strategy->calculate($operationData), $operationData);
    }
}
