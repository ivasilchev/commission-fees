<?php

namespace Ivan\Test\Strategy;

use PHPUnit\Framework\TestCase;
use Money\Money;
use Money\Currency;
use Ivan\Strategy\FixedStrategyResolver;
use Ivan\ValueObject\OperationType;
use Ivan\ValueObject\UserId;
use Ivan\ValueObject\UserType;
use Ivan\Strategy\CashInFee;
use Ivan\Strategy\LegalPersonCashOutFee;
use Ivan\Strategy\NaturalPersonCashOutFee;
use Ivan\ValueObject\OperationData;

class FixedStrategyResolverTest extends TestCase
{
    /** @test */
    public function it_resolves_cash_in_strategy_correctly()
    {
        $cashInStrategy = $this->prophesize(CashInFee::class)->reveal();
        $legalCashOutStrategy = $this->prophesize(LegalPersonCashOutFee::class)->reveal();
        $naturalCashOutStrategy = $this->prophesize(NaturalPersonCashOutFee::class)->reveal();

        $operationData = $this->createOperationData(OperationType::TYPE_CASH_IN, UserType::TYPE_LEGAL);

        $resolver = new FixedStrategyResolver($cashInStrategy, $legalCashOutStrategy, $naturalCashOutStrategy);

        $result = $resolver->resolveFor($operationData);

        $this->assertInstanceOf(CashInFee::class, $result);
    }

    /** @test */
    public function it_resolves_legal_person_cash_out_strategy_correctly()
    {
        $cashInStrategy = $this->prophesize(CashInFee::class)->reveal();
        $legalCashOutStrategy = $this->prophesize(LegalPersonCashOutFee::class)->reveal();
        $naturalCashOutStrategy = $this->prophesize(NaturalPersonCashOutFee::class)->reveal();

        $operationData = $this->createOperationData(OperationType::TYPE_CASH_OUT, UserType::TYPE_LEGAL);

        $resolver = new FixedStrategyResolver($cashInStrategy, $legalCashOutStrategy, $naturalCashOutStrategy);

        $result = $resolver->resolveFor($operationData);

        $this->assertInstanceOf(LegalPersonCashOutFee::class, $result);
    }

    private function createOperationData($operationType, $userType): OperationData
    {
        $time = new \DateTimeImmutable('2011-11-23');
        $operationType = OperationType::fromString($operationType);
        $userId = UserId::fromInt(42);
        $userType = UserType::fromString($userType);
        $currency = new Currency('EUR');
        $amount = new Money(31337, $currency);

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
