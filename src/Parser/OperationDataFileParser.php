<?php

namespace Ivan\Parser;

use Ivan\Repository\OperationDataRepository;
use Ivan\ValueObject\OperationData;
use Ivan\ValueObject\OperationType;
use Ivan\ValueObject\UserId;
use Ivan\ValueObject\UserType;
use Money\Currency;
use Money\Money;
use Money\Parser\DecimalMoneyParser;

class OperationDataFileParser
{
    const TOKEN_SEPARATOR = ',';
    const TOKEN_EXPECTED_COUNT = 6;

    const OPERATION_TIME_POSITION = 0;
    const USER_ID_POSITION = 1;
    const USER_TYPE_POSITION = 2;
    const OPERATION_TYPE_POSITION = 3;
    const AMOUNT_POSITION = 4;
    const CURRENCY_POSITION = 5;

    private $moneyParser;
    private $repository;

    public function __construct(
        DecimalMoneyParser $moneyParser,
        OperationDataRepository $repository)
    {
        $this->moneyParser = $moneyParser;
        $this->repository = $repository;
    }

    public function parseFile(string $inputFilePath): void
    {
        $fileHandle = fopen($inputFilePath, "r");

        if (!$fileHandle) {
            throw new Exception("File not found: $inputFilePath", Exception::ERROR_EMPTY_FILE);
        }

        try {
            while (!feof($fileHandle)) {
                $line = fgets($fileHandle);
                $operationData = $this->parseLine($line);
                $this->repository->insert($operationData);
            }
        } catch (\Assert\InvalidArgumentException $iae) {
            throw new Exception('Semantic error. Invalid token value found.', Exception::ERROR_SEMANTIC_ERROR);
        } catch (\Assert\AssertionFailedException $afe) {
            throw new Exception('Semantic error. Invalid token value found.', Exception::ERROR_SEMANTIC_ERROR);
        } catch (\Exception $e) {
            throw $e;
        } finally {
            fclose($fileHandle);
        }
    }

    private function parseLine(string $line): OperationData
    {
        $tokens = explode(self::TOKEN_SEPARATOR, $line);

        if (count($tokens) !== self::TOKEN_EXPECTED_COUNT) {
            throw new Exception('Invalid number of tokens in input', Exception::ERROR_INVALID_STRUCTURE);
        }

        $tokens = array_map(function($value){
            return trim($value);
        }, $tokens);

        $time = new \DateTimeImmutable($tokens[self::OPERATION_TIME_POSITION]);
        $userId = UserId::fromInt((int)$tokens[self::USER_ID_POSITION]);
        $userType = UserType::fromString($tokens[self::USER_TYPE_POSITION]);
        $operationType = OperationType::fromString($tokens[self::OPERATION_TYPE_POSITION]);
        $currency = new Currency($tokens[self::CURRENCY_POSITION]);
        $amount = $this->parseDecimalValue($tokens[self::AMOUNT_POSITION], $currency);

        return new OperationData(
            $time,
            $operationType,
            $userId,
            $userType,
            $amount,
            $currency
        );
    }

    private function parseDecimalValue(string $value, Currency $currency): Money
    {
        return $this->moneyParser->parse($value, $currency);
    }
}
