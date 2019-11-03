<?php

namespace Ivan\Test\Processor;

use Ivan\Processor\CommissionFeeProcessor;
use Ivan\Strategy\Resolver;
use Ivan\Strategy\CashInFee;
use PHPUnit\Framework\TestCase;
use Ivan\ValueObject\UserId;
use Money\Currency;
use Ivan\ValueObject\OperationType;
use Ivan\ValueObject\UserType;
use Money\Money;
use Ivan\ValueObject\OperationData;
use Prophecy\Argument;

class ComissionFeeProcessorTest extends TestCase
{
    private function getResolverMock(): Resolver
    {
        $resolverMock = $this->prophesize(Resolver::class);
        $strategyMock = $this->prophesize(CashInFee::class);
        $strategyMock->calculate(Argument::any())->willReturn(new Money(31337, new Currency('EUR')));
        $resolverMock->resolveFor(Argument::any())->willReturn($strategyMock->reveal());

        return $resolverMock->reveal();
    }

    /** @test */
    public function it_processes_input_data_with_strategy(): void
    {
        $time = new \DateTimeImmutable('2011-11-23');
        $operationType = OperationType::fromString('cash_in');
        $userId = UserId::fromInt(42);
        $userType = UserType::fromString('legal');
        $currency = new Currency('EUR');
        $amount = new Money(31337, $currency);

        $operationData = new OperationData(
            $time,
            $operationType,
            $userId,
            $userType,
            $amount,
            $currency
        );

        $processor = new CommissionFeeProcessor($this->getResolverMock());

        $result = $processor->process($operationData);

        $this->assertEquals($operationData, $result['operationData']);
        $this->assertEquals(31337, $result['fee']->getAmount());
    }
}
