<?php

namespace Ivan\Money;

use Money\Currency;
use Money\Money;

interface Converter
{
    public function convert(Money $money, Currency $currency, $roundingMode = Money::ROUND_HALF_UP): Money;
}
