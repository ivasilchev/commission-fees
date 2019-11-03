<?php

namespace Ivan\Test\ValueObject;

use Ivan\ValueObject\OperationType;
use PHPUnit\Framework\TestCase;
use Assert\AssertionFailedException;

class OperationTypeTest extends TestCase
{
    /** @test */
    public function it_throws_on_invalid_type_specified(): void
    {
        $this->expectException(AssertionFailedException::class);
        $operationType = OperationType::fromString('invalid-operation-type');
    }

    /** @test */
    public function it_reports_cash_out_type_correctly(): void
    {
        $operationType = OperationType::fromString(OperationType::TYPE_CASH_OUT);

        $this->assertTrue($operationType->isCashOut());
        $this->assertFalse($operationType->isCashIn());
    }

    /** @test */
    public function it_reports_cash_in_type_correctly(): void
    {
        $operationType = OperationType::fromString(OperationType::TYPE_CASH_IN);

        $this->assertTrue($operationType->isCashIn());
        $this->assertFalse($operationType->isCashOut());
    }
}
