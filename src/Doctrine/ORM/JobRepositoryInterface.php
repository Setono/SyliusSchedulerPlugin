<?php

namespace Setono\SyliusSchedulerPlugin\Doctrine\ORM;

use Setono\SyliusSchedulerPlugin\Model\JobInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface JobRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $command
     * @param array $args
     * @return null|JobInterface
     */
    public function findOneByCommand(string $command, array $args = array()): ?JobInterface;

    /**
     * @param string $command
     * @param array $args
     * @return null|JobInterface
     */
    public function findFirstOneByCommand(string $command, array $args = array()): ?JobInterface;

    /**
     * @param array $excludedIds
     * @param array $excludedQueues
     * @param array $restrictedQueues
     * @return null|JobInterface
     */
    public function findOnePending(array $excludedIds = array(), array $excludedQueues = array(), array $restrictedQueues = array()): ?JobInterface;

    /**
     * @param \DateTime $retentionTime
     * @param array $excludedIds
     * @param int $limit
     * @return array|JobInterface[]
     */
    public function findSucceededBefore(\DateTime $retentionTime, array $excludedIds = [], $limit = 100): array;

    /**
     * @param \DateTime $retentionTime
     * @param array $excludedIds
     * @param int $limit
     * @return array|JobInterface[]
     */
    public function findFinishedBefore(\DateTime $retentionTime, array $excludedIds = [], $limit = 100): array;

    /**
     * @param \DateTime $retentionTime
     * @param array $excludedIds
     * @param int $limit
     * @return array|JobInterface[]
     */
    public function findCancelledBefore(\DateTime $retentionTime, array $excludedIds = [], $limit = 100): array;

    /**
     * @param string $workerName
     * @param array $excludedIds
     * @param array $excludedQueues
     * @param array $restrictedQueues
     * @return null|JobInterface
     */
    public function findOneStartableAndAquireLock(string $workerName, array &$excludedIds = array(), $excludedQueues = array(), $restrictedQueues = array()): ?JobInterface;

    /**
     * @param JobInterface $job
     * @return array|JobInterface[]
     */
    public function findIncomingDependencies(JobInterface $job): array;

    /**
     * @param array|int[] $excludedIds
     * @param \DateTime|null $maxAge
     * @return null|JobInterface
     */
    public function findOneStale(array $excludedIds = array(), ?\DateTime $maxAge = null): ?JobInterface;

    /**
     * @param string $workerName
     * @return array|JobInterface[]
     */
    public function findStale(string $workerName): array;

    /**
     * @param int $limit
     * @return array|JobInterface[]
     */
    public function findLastJobsWithError($limit = 10): array;
}