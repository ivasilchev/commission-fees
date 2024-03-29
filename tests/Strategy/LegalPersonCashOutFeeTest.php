<?php

namespace Ivan\Test\Strategy;

use Ivan\Strategy\LegalPersonCashOutFee;
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

class LegalPersonCashOutFeeTest extends TestCase
{
    const FEE_AMOUNT_LIMIT = 5 * 10;
    const FEE_AMOUNT_CURRENCY = 'EUR';
    const CASH_OUT_FEE = 0.003;

    /** @var Converter */
    private $converter;

    public function setUp(): void
    {
        $this->converter = $this->createConverter();
    }

    /** @test */
    public function it_calculates_a_cash_in_fee_correctly(): void
    {
        $maxFeeAmount = new Money(self::FEE_AMOUNT_LIMIT, new Currency(self::FEE_AMOUNT_CURRENCY));

        $strategy = new LegalPersonCashOutFee(self::CASH_OUT_FEE, $maxFeeAmount, $this->converter);

        $feeAmount = $strategy->calculate($this->createOperationData(300 * 100, 'EUR'));

        $this->assertEquals($feeAmount->getAmount(), 90);
    }

    /** @test */
    public function it_calculates_a_cash_out_fee_limit_correctly(): void
    {
        $minFeeAmount = new Money(self::FEE_AMOUNT_LIMIT, new Currency(self::FEE_AMOUNT_CURRENCY));

        $strategy = new LegalPersonCashOutFee(self::CASH_OUT_FEE, $minFeeAmount, $this->converter);

        $feeAmount = $strategy->calculate($this->createOperationData(10, 'EUR'));

        $this->assertTrue($feeAmount->equals($minFeeAmount));
    }

    /** @test */
    public function it_guards_for_correct_operation_type(): void
    {
        $maxFeeAmount = new Money(self::FEE_AMOUNT_LIMIT, new Currency(self::FEE_AMOUNT_CURRENCY));

        $strategy = new LegalPersonCashOutFee(self::CASH_OUT_FEE, $maxFeeAmount, $this->converter);

        $this->expectException(Exception::class);

        $strategy->calculate($this->createOperationData(5 * 100, 'EUR', OperationType::TYPE_CASH_IN));
    }

    private function createOperationData($amount, $currency, $operationType = OperationType::TYPE_CASH_OUT, $userType = UserType::TYPE_LEGAL): OperationData
    {
        $time = new \DateTimeImmutable('2011-11-23');
        $operationType = OperationType::fromString($operationType);
        $userId = UserId::fromInt(42);
        $userType = UserType::fromString($userType);
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
