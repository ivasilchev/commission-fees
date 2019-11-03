<?php

namespace Ivan\Strategy;

use Ivan\Repository\OperationDataRepository;
use Ivan\ValueObject\OperationData;
use Money\Converter;
use Money\Money;
use DateTimeImmutable;

class NaturalPersonCashOutFee implements FeeCalculation
{
    const FREE_OPERATIONS_LIMIT = 3;

    /** @var float */
    private $defaultFeePercent;

    /** @var Money */
    private $freeLimitAmount;

    /** @var OperationDataRepository */
    private $operationsRepository;

    /** @var Converter */
    private $currencyConverter;

    private static $discountsGivenToUsers = [];

    public function __construct(
        float $defaultFeePercent,
        Money $freeLimitAmount,
        OperationDataRepository $operationsRepository,
        Converter $currencyConverter
    ) {
        $this->defaultFeePercent = $defaultFeePercent;
        $this->freeLimitAmount = $freeLimitAmount;
        $this->operationsRepository = $operationsRepository;
        $this->currencyConverter = $currencyConverter;
    }

    public function calculate(OperationData $operationData): Money
    {
        $previousOperations = $this->getPreviousOperations($operationData);
        $previousOperations[] = $operationData;

        $amountSoFarInEUR = $this->sumInEUR($previousOperations);

        $amount = $operationData->getAmount();

        if (count($previousOperations) <= self::FREE_OPERATIONS_LIMIT) {
            if ($amountSoFarInEUR->lessThanOrEqual($this->freeLimitAmount)) {
                return new Money(0, $operationData->getCurrency());
            }

            if ($this->discountWasGiven($operationData) === false) {
                $amount = $amountSoFarInEUR->subtract($this->freeLimitAmount);
                $this->giveDiscount($operationData);
            }

            $amount = $this->currencyConverter->convert($amount, $operationData->getCurrency());
        }

        return new Money(ceil($amount->getAmount() * $this->defaultFeePercent), $amount->getCurrency());
    }

    private function getFirstWeekDayFor(DateTimeImmutable $date): DateTimeImmutable
    {
        return $date->modify('this week monday');
    }

    private function getPreviousOperations(OperationData $operationData): array
    {
        $operations = $this->operationsRepository->findByUserIdAndDateBetween(
            $operationData->getUserId(),
            $this->getFirstWeekDayFor($operationData->getOperationTime()),
            $operationData->getOperationTime()->modify('this week sunday')
        );

        $result = [];

        foreach ($operations as $operation) {
            if ($operation->getUserType()->isNatural() === false
                || $operation->getOperationType()->isCashOut() === false) {
                continue;
            }

            if ($operation->getHash() === $operationData->getHash()) {
                break;
            }

            $result[] = $operation;
        }

        return $result;
    }

    private function giveDiscount(OperationData $operationData): void
    {
        self::$discountsGivenToUsers[] = $this->getOperationHash($operationData);
    }

    private function discountWasGiven(OperationData $operationData): bool
    {
        return in_array($this->getOperationHash($operationData), self::$discountsGivenToUsers) === true;
    }

    private function getOperationHash(OperationData $operationData): string
    {
        return $operationData->getOperationTime()->format("W") . '-' . $operationData->getUserId()->getId();
    }

    private function sumInEUR(array $operations): Money
    {
        $sum = Money::EUR(0);

        foreach ($operations as $operation) {
            $sum = $sum->add($this->currencyConverter->convert($operation->getAmount(), $sum->getCurrency()));
        }

        return $sum;
    }
}
