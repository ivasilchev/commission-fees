<?php

namespace Ivan\Application;

final class Configuration
{
    const CASH_IN_FEE_AMOUNT_LIMIT = 5 * 100;
    const CASH_IN_FEE_AMOUNT_CURRENCY = 'EUR';
    const CASH_IN_FEE = 0.0003;

    const LEGAL_CASH_OUT_FEE_AMOUNT_LIMIT = 5 * 10;
    const LEGAL_CASH_OUT_FEE_AMOUNT_CURRENCY = 'EUR';
    const LEGAL_CASH_OUT_FEE = 0.003;

    const NATURAL_CASH_OUT_FEE_PERCENT = 0.003;
    const NATURAL_CASH_OUT_FEE_LIMIT = 1000 * 100;
    const NATURAL_CASH_OUT_FEE_CURRENCY = 'EUR';

    const EXCHANGE_RATES = [
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
    ];

}