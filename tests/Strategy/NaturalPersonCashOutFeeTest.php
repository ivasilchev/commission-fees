<?php

namespace Ivan\Test\Strategy;

use Ivan\Repository\InMemoryOperationDataRepository;
use Ivan\Repository\OperationDataRepository;
use Ivan\Strategy\NaturalPersonCashOutFee;
use Ivan\ValueObject\OperationData;
use Ivan\ValueObject\OperationType;
use Ivan\ValueObject\UserId;
use Ivan\ValueObject\UserType;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Ivan\Money\Converter;
use Prophecy\Argument;

class NaturalPersonCashOutFeeTest extends TestCase
{
    const DEFAULT_FEE_PERCENT = 0.003;

    /** @var Converter */
    private $converter;

    public function setUp(): void
    {
        $this->converter = $this->createConverter();
    }

    /** @test */
    public function it_applies_1000_EUR_free_of_charge_rule(): void
    {
        $operations = [
            $this->createOperationData('2011-11-23', 100, 'EUR'),
            $this->createOperationData('2011-11-23', 100, 'EUR')
        ];

        $repository = $this->getRepository($operations);

        $strategy = new NaturalPersonCashOutFee(
            self::DEFAULT_FEE_PERCENT,
            Money::EUR(1000 * 100),
            $repository,
            $this->converter
        );

        $fee = $strategy->calculate($this->createOperationData('2011-11-23', 100, 'EUR'));

        $this->assertTrue($fee->equals(Money::EUR(0)));
    }

    private function createConverter(): Converter
    {
        $converter = $this->prophesize(Converter::class);
        $converter->convert(Argument::cetera())->willReturnArgument(0);
        return $converter->reveal();
    }

    private function createOperationData($date, $amount, $currency): OperationData
    {
        $currency = new Currency($currency);

        return new OperationData(
            new \DateTimeImmutable($date),
            OperationType::fromString(OperationType::TYPE_CASH_OUT),
            UserId::fromInt(42),
            UserType::fromString(UserType::TYPE_NATURAL),
            new Money($amount, $currency),
            $currency
        );
    }

    private function getRepository(array $operations): OperationDataRepository
    {
        $repository = new InMemoryOperationDataRepository();
        foreach ($operations as $operation) {
            $repository->insert($operation);
        }
        return $repository;
    }
}
