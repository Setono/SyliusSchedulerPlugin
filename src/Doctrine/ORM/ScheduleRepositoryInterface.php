<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusSchedulerPlugin\Model\ScheduleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface ScheduleRepositoryInterface extends RepositoryInterface
{
    /**
     * @return QueryBuilder
     */
    public function createOrderedQueryBuilder(): QueryBuilder;

    /**
     * @param array $restrictedQueues
     *
     * @return array|ScheduleInterface[]
     */
    public function findByQueues(array $restrictedQueues = []): array;
}
