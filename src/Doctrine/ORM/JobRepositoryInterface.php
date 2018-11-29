<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusSchedulerPlugin\Model\JobInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface JobRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $id
     * @param string $scheduleId
     *
     * @return JobInterface|null
     */
    public function findOneByIdAndScheduleId(string $id, string $scheduleId): ?JobInterface;

    /**
     * @param string $scheduleId
     *
     * @return QueryBuilder
     */
    public function createQueryBuilderByScheduleId(string $scheduleId): QueryBuilder;

    /**
     * @param string $originalJobId
     *
     * @return QueryBuilder
     */
    public function createQueryBuilderByOriginalJobId(string $originalJobId): QueryBuilder;

    /**
     * @param string $command
     * @param array $args
     *
     * @return JobInterface|null
     */
    public function findOneByCommand(string $command, array $args = []): ?JobInterface;

    /**
     * @param string $command
     * @param array $args
     *
     * @return JobInterface|null
     */
    public function findFirstOneByCommand(string $command, array $args = []): ?JobInterface;

    /**
     * @param array $excludedIds
     * @param array $excludedQueues
     * @param array $restrictedQueues
     *
     * @return JobInterface|null
     */
    public function findOnePending(array $excludedIds = [], array $excludedQueues = [], array $restrictedQueues = []): ?JobInterface;

    /**
     * @param \DateTime $retentionTime
     * @param array $excludedIds
     * @param int $limit
     *
     * @return array|JobInterface[]
     */
    public function findSucceededBefore(\DateTime $retentionTime, array $excludedIds = [], $limit = 100): array;

    /**
     * @param \DateTime $retentionTime
     * @param array $excludedIds
     * @param int $limit
     *
     * @return array|JobInterface[]
     */
    public function findFinishedBefore(\DateTime $retentionTime, array $excludedIds = [], $limit = 100): array;

    /**
     * @param \DateTime $retentionTime
     * @param array $excludedIds
     * @param int $limit
     *
     * @return array|JobInterface[]
     */
    public function findCancelledBefore(\DateTime $retentionTime, array $excludedIds = [], $limit = 100): array;

    /**
     * @param string $workerName
     * @param array $excludedIds
     * @param array $excludedQueues
     * @param array $restrictedQueues
     *
     * @return JobInterface|null
     */
    public function findOneStartableAndAquireLock(string $workerName, array &$excludedIds = [], $excludedQueues = [], $restrictedQueues = []): ?JobInterface;

    /**
     * @param JobInterface $job
     *
     * @return array|JobInterface[]
     */
    public function findIncomingDependencies(JobInterface $job): array;

    /**
     * @param array|int[] $excludedIds
     * @param \DateTime|null $maxAge
     *
     * @return JobInterface|null
     */
    public function findOneStale(array $excludedIds = [], ?\DateTime $maxAge = null): ?JobInterface;

    /**
     * @param string $workerName
     *
     * @return array|JobInterface[]
     */
    public function findStale(string $workerName): array;

    /**
     * @param int $limit
     *
     * @return array|JobInterface[]
     */
    public function findLastJobsWithError($limit = 10): array;
}
