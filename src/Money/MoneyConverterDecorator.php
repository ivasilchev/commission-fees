<?php

namespace Ivan\Money;

use Money\Converter as MoneyConverter;
use Money\Currency;
use Money\Money;

class MoneyConverterDecorator implements Converter
{
    private $converter;

    public function __construct(MoneyConverter $converter)
    {
        $this->converter = $converter;
    }

    public function convert(Money $money, Currency $currency, $roundingMode = Money::ROUND_HALF_UP): Money
    {
        return $this->converter->convert($money, $currency, $roundingMode);
    }
}
