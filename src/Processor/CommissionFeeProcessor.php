<?php

namespace Ivan\Processor;

use Ivan\Strategy\Resolver;
use Ivan\ValueObject\OperationData;

class CommissionFeeProcessor implements FeeProcessor
{
    private $strategyResolver;

    public function __construct(Resolver $strategyResolver)
    {
        $this->strategyResolver = $strategyResolver;
    }

    public function process(OperationData $operationData): array
    {
        $strategy = $this->strategyResolver->resolveFor($operationData);
        return [
            'operationData' => $operationData,
            'fee' => $strategy->calculate($operationData)
        ];
    }
}
