<?php

namespace Ivan\Test\ValueObject;

use Ivan\ValueObject\OperationData;
use Ivan\ValueObject\OperationType;
use Ivan\ValueObject\UserId;
use Ivan\ValueObject\UserType;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class OperationDataTest extends TestCase
{
    const OPERATION_TIME_STRING = '2011-11-23';
    const OPERATION_TYPE = OperationType::TYPE_CASH_IN;
    const USER_ID = 42;
    const USER_TYPE = UserType::TYPE_LEGAL;
    const AMOUNT = 31337;
    const CURRENCY = 'EUR';

    /** @test */
    public function it_returns_values_correctly(): void
    {
        $operationData = $this->createOperationData();
        $time = new \DateTimeImmutable(self::OPERATION_TIME_STRING);

        $this->assertEquals(0, $time->diff($operationData->getOperationTime())->d);
        $this->assertEquals(0, $time->diff($operationData->getOperationTime())->m);
        $this->assertEquals(0, $time->diff($operationData->getOperationTime())->y);
        $this->assertEquals(0, $time->diff($operationData->getOperationTime())->i);
        $this->assertEquals(0, $time->diff($operationData->getOperationTime())->h);
        $this->assertEquals(0, $time->diff($operationData->getOperationTime())->s);

        $this->assertTrue($operationData->getOperationType()->isCashIn());

        $this->assertEquals(self::USER_ID, $operationData->getUserId()->getId());

        $this->assertEquals(self::USER_TYPE, $operationData->getUserType()->getType());

        $this->assertTrue($operationData->getAmount()->equals(Money::EUR(self::AMOUNT)));

        $this->assertTrue($operationData->getCurrency()->equals(new Currency(self::CURRENCY)));
    }
    /** @test */
    public function it_creates_a_unique_hash(): void
    {
        $operationData = [];
        $operationData[0] = $this->createOperationData();
        $operationData[1] = $this->createOperationData();

        $this->assertNotEquals($operationData[0]->getHash(), $operationData[1]->getHash());
    }

    private function createOperationData(): OperationData
    {
        $time = new \DateTimeImmutable(self::OPERATION_TIME_STRING);
        $operationType = OperationType::fromString(self::OPERATION_TYPE);
        $userId = UserId::fromInt(self::USER_ID);
        $userType = UserType::fromString(self::USER_TYPE);
        $currency = new Currency(self::CURRENCY);
        $amount = new Money(self::AMOUNT, $currency);

        return new OperationData(
            $time,
            $operationType,
            $userId,
            $userType,
            $amount,
            $currency
        );
    }
}
