<?php

namespace Ivan\Test\Strategy;

use Ivan\Strategy\CashInFee;
use Ivan\ValueObject\OperationType;
use Ivan\ValueObject\UserType;
use Ivan\Money\Converter;

use PHPUnit\Framework\TestCase;
use Ivan\ValueObject\OperationData;
use Ivan\ValueObject\UserId;
use Money\Currency;
use Money\Money;
use Money\Exchange;
use Ivan\Strategy\Exception;
use Prophecy\Argument;

class CashInFeeTest extends TestCase
{
    const FEE_AMOUNT_LIMIT = 5 * 100;
    const FEE_AMOUNT_CURRENCY = 'EUR';
    const CASH_IN_FEE = 0.003;

    private $converter;

    public function setUp(): void
    {
        $this->converter = $this->createConverter();
    }

    /** @test */
    public function it_calculates_a_cash_in_fee_correctly(): void
    {
        $maxFeeAmount = new Money(self::FEE_AMOUNT_LIMIT, new Currency(self::FEE_AMOUNT_CURRENCY));

        $strategy = new CashInFee(self::CASH_IN_FEE, $maxFeeAmount, $this->converter);

        $feeAmount = $strategy->calculate($this->createOperationData(200 * 100, 'EUR'));

        $this->assertEquals($feeAmount->getAmount(), 60);
    }

    /** @test */
    public function it_calculates_a_cash_in_fee_limit_correctly(): void
    {
        $maxFeeAmount = new Money(self::FEE_AMOUNT_LIMIT, new Currency(self::FEE_AMOUNT_CURRENCY));

        $strategy = new CashInFee(self::CASH_IN_FEE, $maxFeeAmount, $this->converter);

        $feeAmount = $strategy->calculate($this->createOperationData(200000000 * 100, 'EUR'));

        $this->assertTrue($feeAmount->equals($maxFeeAmount));
    }

    /** @test */
    public function it_guards_for_correct_operation_type(): void
    {
        $maxFeeAmount = new Money(self::FEE_AMOUNT_LIMIT, new Currency(self::FEE_AMOUNT_CURRENCY));

        $strategy = new CashInFee(self::CASH_IN_FEE, $maxFeeAmount, $this->converter);

        $this->expectException(Exception::class);

        $strategy->calculate($this->createOperationData(5 * 100, 'EUR', OperationType::TYPE_CASH_OUT));
    }

    private function createOperationData($amount, $currency, $operationType = OperationType::TYPE_CASH_IN): OperationData
    {
        $time = new \DateTimeImmutable('2011-11-23');
        $operationType = OperationType::fromString($operationType);
        $userId = UserId::fromInt(42);
        $userType = UserType::fromString(UserType::TYPE_NATURAL);
        $currency = new Currency($currency);
        $amount = new Money($amount, $currency);

        return new OperationData(
            $time,
            $operationType,
            $userId,
            $userType,
            $amount,
            $currency
        );
    }

    private function createConverter(): Converter
    {
        $converter = $this->prophesize(Converter::class);
        $converter->convert(Argument::cetera())->willReturnArgument(0);

        return $converter->reveal();
    }
}
