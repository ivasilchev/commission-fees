<?php

namespace Ivan\Test\Repository;

use Ivan\Repository\InMemoryOperationDataRepository;
use PHPUnit\Framework\TestCase;
use Ivan\ValueObject\OperationType;
use Ivan\ValueObject\UserId;
use Ivan\ValueObject\UserType;
use Money\Money;
use Money\Currency;
use Ivan\ValueObject\OperationData;

class InMemoryOperationDataRepositoryTest extends TestCase
{
    /** @test */
    public function it_inserts_a_record(): void
    {
        $repository = new InMemoryOperationDataRepository();

        $data = $this->createOperationData('2019-01-01', 42);

        $repository->insert($data);

        $all = $repository->findAll();

        $this->assertEquals($all[0], $data);
    }

    /** @test */
    public function it_finds_all_records(): void
    {
        $repository = new InMemoryOperationDataRepository();

        $repository->insert($this->createOperationData('2019-01-01', 42));
        $repository->insert($this->createOperationData('2019-01-01', 42));
        $repository->insert($this->createOperationData('2019-01-01', 42));
        $repository->insert($this->createOperationData('2019-01-01', 42));
        $repository->insert($this->createOperationData('2019-01-01', 42));

        $this->assertEquals(5, count($repository->findAll()));
    }

    /** @test */
    public function it_finds_data_for_user_and_a_date_period(): void
    {
        $repository = new InMemoryOperationDataRepository();

        $repository->insert($this->createOperationData('2019-01-01', 42));
        $repository->insert($this->createOperationData('2019-01-02', 42));
        $repository->insert($this->createOperationData('2019-01-03', 42));
        $repository->insert($this->createOperationData('2019-01-04', 43));
        $repository->insert($this->createOperationData('2019-01-04', 45));
        $repository->insert($this->createOperationData('2019-01-05', 42));
        $repository->insert($this->createOperationData('2019-01-05', 46));
        $repository->insert($this->createOperationData('2019-01-05', 46));
        $repository->insert($this->createOperationData('2019-01-06', 42));

        $results = $repository->findByUserIdAndDateBetween(
            UserId::fromInt(42),
            new \DateTimeImmutable('2019-01-01'),
            new \DateTimeImmutable('2019-01-05')
        );

        $this->assertEquals(4, count($results));
    }

    private function createOperationData($dateString, $userId): OperationData
    {
        $time = new \DateTimeImmutable($dateString);
        $operationType = OperationType::fromString(OperationType::TYPE_CASH_IN);
        $userId = UserId::fromInt($userId);
        $userType = UserType::fromString(UserType::TYPE_LEGAL);
        $currency = new Currency('EUR');
        $amount = new Money(1337, $currency);

        return new OperationData(
            $time,
            $operationType,
            $userId,
            $userType,
            $amount,
            $currency
        );
    }
}
