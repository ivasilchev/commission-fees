<?php

namespace Ivan\ValueObject;

use Assert\Assertion;

final class UserType
{
    const TYPE_LEGAL = 'legal';
    const TYPE_NATURAL = 'natural';

    /** @var string */
    private $type;

    public static function fromString(string $type): UserType
    {
        return new self($type);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isNatural(): bool
    {
        return $this->type === self::TYPE_NATURAL;
    }

    public function isLegal(): bool
    {
        return $this->type === self::TYPE_LEGAL;
    }

    private function validateType(string $type): void
    {
        Assertion::inArray($type, [self::TYPE_LEGAL, self::TYPE_NATURAL]);
    }

    private function __construct(string $type)
    {
        $this->validateType($type);
        $this->type = $type;
    }
}
