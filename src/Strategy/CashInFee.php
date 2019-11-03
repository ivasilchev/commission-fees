<?php

namespace Ivan\Strategy;

use Ivan\ValueObject\OperationData;
use Money\Converter;
use Money\Money;

class CashInFee implements FeeCalculation
{
    /** @var float */
    private $feeAmountPercent;

    /** @var Money */
    private $maxFeeAmountLimit;

    /** @var Converter */
    private $currencyConverter;

    public function __construct(float $feeAmountPercent, Money $maxFeeAmountLimit, Converter $currencyConverter)
    {
        $this->feeAmountPercent = $feeAmountPercent;
        $this->maxFeeAmountLimit = $maxFeeAmountLimit;
        $this->currencyConverter = $currencyConverter;
    }

    public function calculate(OperationData $operationData): Money
    {
        $this->guardOperationType($operationData);

        $feeAmount = ceil($operationData->getAmount()->getAmount() * $this->feeAmountPercent);

        $result = new Money((int)$feeAmount, $operationData->getAmount()->getCurrency());

        return $this->applyUpperLimit($result);
    }

    private function applyUpperLimit(Money $amount): Money
    {
        $convertedAmount = $this->currencyConverter->convert($amount, $this->maxFeeAmountLimit->getCurrency(), Money::ROUND_UP);

        if ($convertedAmount->greaterThan($this->maxFeeAmountLimit)) {
           return $this->currencyConverter->convert($this->maxFeeAmountLimit, $amount->getCurrency(), Money::ROUND_UP);
        }

        return $amount;
    }

    private function guardOperationType(OperationData $operationData): void
    {
        if (!$operationData->getOperationType()->isCashIn()) {
            throw new Exception('Incorrect operation type data for cash-in fee calculation');
        }
    }
}
