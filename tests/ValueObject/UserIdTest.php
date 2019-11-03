<?php

namespace Ivan\Test\ValueObject;

use Assert\AssertionFailedException;
use Ivan\ValueObject\UserId;
use PHPUnit\Framework\TestCase;

class UserIdTest extends TestCase
{
    const TEST_USER_ID = 1337;

    /** @test */
    public function it_throws_on_invalid_int_id(): void
    {
        $this->expectException(AssertionFailedException::class);
        $userId = UserId::fromInt(0);
    }

    /** @test */
    public function it_capsulates_value_properly(): void
    {
        $userId = UserId::fromInt(self::TEST_USER_ID);
        $this->assertIsInt($userId->getId());
        $this->assertEquals(self::TEST_USER_ID, $userId->getId());
    }
}
