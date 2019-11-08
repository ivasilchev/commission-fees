<?php

namespace Ivan\Test\Functional\Money;

use PHPUnit\Framework\TestCase;
use Ivan\Money\MoneyConverterDecorator;
use Money\Currency;
use Money\Money;
use Money\Converter as MoneyConverter;
use Money\Currencies\ISOCurrencies;
use Money\Exchange\FixedExchange;

class MoneyConverterDecoratorTest extends TestCase
{
    /**
     * @test
     * @group functional
     */
    public function it_converts_between_currencies(): void
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

        $moneyConverterDecorator = new MoneyConverterDecorator($moneyConverter);

        $cash = new Money(1337, new Currency('EUR'));

        $cashDecorated = $moneyConverterDecorator->convert($cash, new Currency('JPY'));
        $cashOriginal = $moneyConverter->convert($cash, new Currency('JPY'));

        $this->assertTrue($cashDecorated->equals($cashOriginal));
    }
}
