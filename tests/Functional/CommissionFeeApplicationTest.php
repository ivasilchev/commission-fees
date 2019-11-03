<?php

namespace Ivan\Test\Processor;

use Ivan\Application\CommissionFeeApplication;
use Ivan\Application\Configuration;
use PHPUnit\Framework\TestCase;

class CommissionFeeApplicationTest extends TestCase
{
    const EXPECTED_RESULTS = [
        0.60,
        3.00,
        0.00,
        0.06,
        0.90,
        0.00,
        0.70,
        0.30,
        0.30,
        5.00,
        0.00,
        0.00,
        8612.0
    ];

    /** @test */
    public function it_processes_operations_correctly(): void
    {
        $application = new CommissionFeeApplication(new Configuration());
        $output = $application->runWithInputFile(getcwd().'/tests/fixtures/valid-input.csv');

        $this->assertEquals(self::EXPECTED_RESULTS, $output);
    }
}
