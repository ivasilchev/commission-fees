<?php

namespace Ivan\Test\Functional\Strategy;

use Ivan\Money\MoneyConverterDecorator;
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
use Money\Converter as MoneyConverter;
use Money\Currencies\ISOCurrencies;
use Money\Exchange\FixedExchange;
use Ivan\Money\Converter;

class NaturalPersonCashOutFeeTest extends TestCase
{
    const DEFAULT_FEE_PERCENT = 0.003;

    /**
     * @test
     * @group functional
     */
    public function it_calculates_default_fee_for_operations_after_the_third(): void
    {
        $operations = [
            $this->createOperationData('2016-01-06', 30000, 'JPY'),
            $this->createOperationData('2016-01-07', 1000 * 100, 'EUR'),
            $this->createOperationData('2016-01-07', 100 * 100 , 'USD'),
            $this->createOperationData('2016-01-10', 100 * 100, 'EUR')
        ];

        $repository = $this->getRepository($operations);

        $strategy = new NaturalPersonCashOutFee(
            self::DEFAULT_FEE_PERCENT,
            Money::EUR(1000 * 100),
            $repository,
            $this->getConverter()
        );

        $result = [];

        foreach ($repository->findAll() as $operation) {
            $fee = $strategy->calculate($operation);
            $result[] = $fee->getAmount();
        }

        $expected = [
            0,
            70,
            30,
            30,
        ];

        $this->assertEquals($expected, $result);
    }

    private function getConverter(): Converter
    {
        $exchange = new FixedExchange([
            'EUR' => [
                'EUR' => 1,
                'USD' => 1.1497,
                'JPY' => 129.53
            ],
            'JPY' => [
                'JPY' => 1,
                'EUR' => 1 / 129.53
            ],
            'USD' => [
                'USD' => 1,
                'EUR' => 1 / 1.1497,
            ]
        ]);

        $moneyConverter = new MoneyConverter(new ISOCurrencies(), $exchange);

        return new MoneyConverterDecorator($moneyConverter);
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
