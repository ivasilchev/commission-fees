<?php

namespace Ivan\Repository;

use Ivan\ValueObject\OperationData;
use Ivan\ValueObject\UserId;
use DateTimeImmutable;

interface OperationDataRepository
{
    public function findAll(): array;
    public function findByUserIdAndDateBetween(UserId $userId, DateTimeImmutable $startDate, DateTimeImmutable $endDate): array;
    public function insert(OperationData $operationData): void;
}
