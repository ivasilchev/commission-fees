<?php

namespace Ivan\Application;

use Ivan\Strategy\FixedStrategyResolver;
use Ivan\Repository\InMemoryOperationDataRepository;
use Ivan\Strategy\Resolver;
use Money\Converter;
use Money\Exchange\FixedExchange;
use Money\Currencies\ISOCurrencies;
use Ivan\Strategy\CashInFee;
use Ivan\Strategy\NaturalPersonCashOutFee;
use Ivan\Strategy\LegalPersonCashOutFee;
use Money\Money;
use Money\Currency;
use Money\Parser\DecimalMoneyParser;
use Ivan\Parser\OperationDataFileParser;
use Money\Formatter\DecimalMoneyFormatter;
use Ivan\Processor\CommissionFeeProcessor;

class CommissionFeeApplication
{
    /** @var Configuration */
    private $configuration;

    private $currencyConverter;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
        $this->currencyConverter = $this->createConverter();
    }

    public function runWithInputFile(string $filePath): array
    {
        $repository = new InMemoryOperationDataRepository();
        $moneyParser = new DecimalMoneyParser(new ISOCurrencies());
        $parser = new OperationDataFileParser($moneyParser, $repository);

        $parser->parseFile($filePath);

        $formatter = new DecimalMoneyFormatter(new ISOCurrencies());

        $output = [];

        $processor = new CommissionFeeProcessor($this->createResolver($repository));

        foreach ($repository->findAll() as $operationData) {
            $fee =  $processor->process($operationData);
            $output[] = (float)$formatter->format($fee['fee']);
        }

        return $output;
    }

    private function createResolver(InMemoryOperationDataRepository $repository): Resolver
    {
        return new FixedStrategyResolver(
            $this->createCashInFeeStrategy(),
            $this->createLegalPersonCashoutStrategy(),
            $this->createNaturalPersonCashOutStrategy($repository)
        );
    }

    private function createConverter(): Converter
    {
        $exchange = new FixedExchange($this->configuration::EXCHANGE_RATES);

        return new Converter(new ISOCurrencies(), $exchange);
    }

    private function createCashInFeeStrategy(): CashInFee
    {
        return new CashInFee(
            $this->configuration::CASH_IN_FEE,
            new Money($this->configuration::CASH_IN_FEE_AMOUNT_LIMIT, new Currency($this->configuration::CASH_IN_FEE_AMOUNT_CURRENCY)),
            $this->currencyConverter
        );
    }

    private function createLegalPersonCashoutStrategy(): LegalPersonCashOutFee
    {
        return new LegalPersonCashOutFee(
            $this->configuration::LEGAL_CASH_OUT_FEE,
            new Money($this->configuration::LEGAL_CASH_OUT_FEE_AMOUNT_LIMIT, new Currency($this->configuration::LEGAL_CASH_OUT_FEE_AMOUNT_CURRENCY)),
            $this->currencyConverter
        );
    }

    private function createNaturalPersonCashOutStrategy(InMemoryOperationDataRepository $repository): NaturalPersonCashOutFee
    {
        return new NaturalPersonCashOutFee(
            $this->configuration::NATURAL_CASH_OUT_FEE_PERCENT,
            new Money($this->configuration::NATURAL_CASH_OUT_FEE_LIMIT, new Currency($this->configuration::NATURAL_CASH_OUT_FEE_CURRENCY)),
            $repository,
            $this->currencyConverter
        );
    }
}
