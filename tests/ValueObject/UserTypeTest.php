<?php

namespace Ivan\Test\ValueObject;

use Ivan\ValueObject\UserType;
use PHPUnit\Framework\TestCase;
use Assert\AssertionFailedException;

class UserTypeTest extends TestCase
{
    const VALID_TEST_TYPE_VALUE = 'legal';
    const INVALID_TEST_TYPE_VALUE = 'so-invalid';

    /** @test */
    public function it_capsulates_the_type_correctly(): void
    {
        $userType = UserType::fromString(self::VALID_TEST_TYPE_VALUE);
        $this->assertEquals(self::VALID_TEST_TYPE_VALUE, $userType->getType());
    }

    /** @test */
    public function it_throws_on_invalid_type_string(): void
    {
        $this->expectException(AssertionFailedException::class);
        $userType = UserType::fromString(self::INVALID_TEST_TYPE_VALUE);
    }

    /** @test */
    public function it_returns_correct_fluent_type_legal(): void
    {
        $userType = UserType::fromString(UserType::TYPE_LEGAL);
        $this->assertTrue($userType->isLegal());
        $this->assertFalse($userType->isNatural());
    }

    /** @test */
    public function it_returns_correct_fluent_type_natural(): void
    {
        $userType = UserType::fromString(UserType::TYPE_NATURAL);
        $this->assertTrue($userType->isNatural());
        $this->assertFalse($userType->isLegal());
    }
}
