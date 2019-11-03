<?php

namespace Ivan\Test\Parser;

use Ivan\Parser\Exception;
use Ivan\Repository\InMemoryOperationDataRepository;
use Ivan\Parser\OperationDataFileParser;
use Money\Currencies\ISOCurrencies;
use Money\Parser\DecimalMoneyParser;
use PHPUnit\Framework\TestCase;

class OperationDataFileParserTest extends TestCase
{
    /** @test */
    public function it_parses_a_valid_file(): void
    {
        $repository = new InMemoryOperationDataRepository();
        $moneyParser = new DecimalMoneyParser(new ISOCurrencies());
        $parser = new OperationDataFileParser($moneyParser, $repository);

        $filePath = getcwd().'/tests/fixtures/valid-input.csv';

        $parser->parseFile($filePath);

        $this->assertGreaterThan(0,count($repository->findAll()));
    }

    /** @test */
    public function it_throws_on_semantic_error(): void
    {
        $repository = new InMemoryOperationDataRepository();
        $moneyParser = new DecimalMoneyParser(new ISOCurrencies());
        $parser = new OperationDataFileParser($moneyParser, $repository);

        $filePath = getcwd().'/tests/fixtures/invalid-input-semantic-errors.csv';

        $this->expectException(Exception::class);
        $this->expectExceptionCode(Exception::ERROR_SEMANTIC_ERROR);
        $parser->parseFile($filePath);
    }

    /** @test */
    public function it_throws_on_file_structure_error(): void
    {
        $repository = new InMemoryOperationDataRepository();
        $moneyParser = new DecimalMoneyParser(new ISOCurrencies());
        $parser = new OperationDataFileParser($moneyParser, $repository);

        $filePath = getcwd().'/tests/fixtures/invalid-input-format-structure.csv';

        $this->expectException(Exception::class);
        $this->expectExceptionCode(Exception::ERROR_INVALID_STRUCTURE);
        $parser->parseFile($filePath);
    }
}
