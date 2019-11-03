<?php

namespace Ivan\ValueObject;

use Assert\Assertion;

class OperationType
{
    const TYPE_CASH_IN = 'cash_in';
    const TYPE_CASH_OUT = 'cash_out';

    /** @var string */
    private $type;

    public static function fromString(string $typeString): OperationType
    {
        return new self($typeString);
    }

    public function isCashIn(): bool
    {
        return $this->type === self::TYPE_CASH_IN;
    }

    public function isCashOut(): bool
    {
        return $this->type === self::TYPE_CASH_OUT;
    }

    private function __construct(string $operationType)
    {
        $this->guardType($operationType);
        $this->type = $operationType;
    }

    private function guardType(string $type)
    {
        Assertion::inArray($type, [self::TYPE_CASH_IN, self::TYPE_CASH_OUT]);
    }
}
