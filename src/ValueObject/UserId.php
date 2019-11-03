<?php

namespace Ivan\ValueObject;

use Assert\Assertion;

final class UserId
{
    /** @var int */
    private $id;

    public static function fromInt(int $integerId): UserId
    {
        return new self($integerId);
    }

    private function __construct(int $id)
    {
        $this->validateId($id);
        $this->id = $id;
    }

    private function validateId($id): void
    {
        Assertion::min($id, 1);
    }

    public function getId(): int
    {
        return $this->id;
    }
}
