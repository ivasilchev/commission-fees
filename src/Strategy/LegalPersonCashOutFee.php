<?php

namespace Ivan\Strategy;

use Ivan\ValueObject\OperationData;
use Money\Converter;
use Money\Money;

class LegalPersonCashOutFee implements FeeCalculation
{
    /** @var float */
    private $feeAmountPercent;

    /** @var Money */
    private $minFeeAmountLimit;

    /** @var Converter */
    private $currencyConverter;

    public function __construct(float $feeAmountPercent, Money $minFeeAmountLimit, Converter $currencyConverter)
    {
        $this->feeAmountPercent = $feeAmountPercent;
        $this->minFeeAmountLimit = $minFeeAmountLimit;
        $this->currencyConverter = $currencyConverter;
    }

    public function calculate(OperationData $operationData): Money
    {
        $this->guardOperationType($operationData);
        $this->guardUserType($operationData);

        $feeAmount = ceil($operationData->getAmount()->getAmount() * $this->feeAmountPercent);

        $result = new Money((int)$feeAmount, $operationData->getAmount()->getCurrency());

        return $this->applyUpperLimit($result);
    }

    private function applyUpperLimit(Money $amount): Money
    {
        $convertedAmount = $this->currencyConverter->convert($amount, $this->minFeeAmountLimit->getCurrency(), Money::ROUND_UP);

        if ($convertedAmount->lessThan($this->minFeeAmountLimit)) {
           return $this->currencyConverter->convert($this->minFeeAmountLimit, $amount->getCurrency(), Money::ROUND_UP);
        }

        return $amount;
    }

    private function guardOperationType(OperationData $operationData): void
    {
        if (!$operationData->getOperationType()->isCashOut()) {
            throw new Exception('Incorrect operation type data for cash-out fee calculation');
        }
    }

    private function guardUserType(OperationData $operationData): void
    {
        if (!$operationData->getUserType()->isLegal()) {
            throw new Exception('Incorrect user type data for cash-out fee calculation');
        }
    }
}
