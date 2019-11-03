<?php

namespace Ivan\Repository;

use Ivan\ValueObject\OperationData;
use Ivan\ValueObject\UserId;
use DateTimeImmutable;

class InMemoryOperationDataRepository implements OperationDataRepository
{
    /** @var OperationData[] */
    private $records = [];

    /** {@inheritdoc} */
    public function findAll(): array
    {
        return $this->records;
    }

    /** {@inheritdoc} */
    public function insert(OperationData $operationData): void
    {
        $this->records[] = $operationData;
    }

    /** {@inheritdoc} */
    public function findByUserIdAndDateBetween(UserId $userId, DateTimeImmutable $startDate, DateTimeImmutable $endDate): array
    {
        $results = [];
        foreach ($this->findAll() as $operationData) {
            if (
                $userId->getId() === $operationData->getUserId()->getId()
                && $this->dateBetween($operationData->getOperationTime(), $startDate, $endDate)
            ) {
                $results[] = $operationData;
            }
        }

        return $results;
    }

    private function dateBetween(
        DateTimeImmutable $date,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate
    ): bool
    {
        return $date >= $startDate && $date <= $endDate;
    }
}
