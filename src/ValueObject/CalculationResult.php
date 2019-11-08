<?php

namespace Ivan\ValueObject;

use Money\Money;

class CalculationResult
{
    /** @var Money */
    private $feeAmount;

    /** @var OperationData */
    private $sourceData;

    public function __construct(Money $feeAmount, OperationData $sourceData)
    {
        $this->feeAmount = $feeAmount;
        $this->sourceData = $sourceData;
    }

    public function getSourceData(): OperationData
    {
        return $this->sourceData;
    }

    public function getFeeAmount(): Money
    {
        return $this->feeAmount;
    }
}
