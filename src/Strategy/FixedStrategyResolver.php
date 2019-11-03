<?php

namespace Ivan\Strategy;

use Ivan\ValueObject\OperationData;

class FixedStrategyResolver implements Resolver
{
    private $cashInFeeStrategy;
    private $legalPersonCashOutStrategy;
    private $naturalPersonCashOutFeeStrategy;

    public function __construct(
        CashInFee $cashInFeeStrategy,
        LegalPersonCashOutFee $legalPersonCashOutFeeStrategy,
        NaturalPersonCashOutFee $naturalPersonCashOutFeeStrategy)
    {
        $this->cashInFeeStrategy = $cashInFeeStrategy;
        $this->legalPersonCashOutStrategy = $legalPersonCashOutFeeStrategy;
        $this->naturalPersonCashOutFeeStrategy = $naturalPersonCashOutFeeStrategy;
    }

    public function resolveFor(OperationData $operationData): FeeCalculation
    {
        if ($operationData->getOperationType()->isCashIn()) {
            return $this->cashInFeeStrategy;
        }

        if ($operationData->getOperationType()->isCashOut()) {
            if ($operationData->getUserType()->isLegal()) {
                return $this->legalPersonCashOutStrategy;
            }

            if ($operationData->getUserType()->isNatural()) {
                return $this->naturalPersonCashOutFeeStrategy;
            }
        }

        throw new Exception('Unable to resolve strategy for input data');
    }
}
