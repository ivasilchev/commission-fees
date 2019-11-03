<?php

namespace Ivan\ValueObject;

use DateTimeImmutable;
use Money\Money;
use Money\Currency;

final class OperationData
{
    /** @var  DateTimeImmutable */
    private $operationTime;

    /** @var UserId */
    private $userId;

    /** @var UserType */
    private $userType;

    /** @var OperationType */
    private $operationType;

    /** @var Money */
    private $amount;

    /** @var Currency */
    private $currency;

    private $hash;

    static $hashAccumulator = 0 ;

    public function __construct(
        DateTimeImmutable $operationTime,
        OperationType $operationType,
        UserId $userId,
        UserType $userType,
        Money $amount,
        Currency $currency
    ) {
        $this->operationTime = $operationTime;
        $this->operationType = $operationType;
        $this->userId = $userId;
        $this->userType = $userType;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->hash = $this->createHash();
    }

    public function getOperationTime(): DateTimeImmutable
    {
        return $this->operationTime;
    }

    public function getOperationType(): OperationType
    {
        return $this->operationType;
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function getUserType(): UserType
    {
        return $this->userType;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getHash(): int
    {
        return $this->hash;
    }

    private function createHash(): int
    {
        self::$hashAccumulator++;
        return self::$hashAccumulator;
    }
}
