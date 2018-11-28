<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Doctrine\ORM;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\QueryBuilder;
use Setono\SyliusSchedulerPlugin\Model\JobInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class JobRepository extends EntityRepository implements JobRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findOneByIdAndScheduleId(string $id, string $scheduleId): ?JobInterface
    {
        return $this->createQueryBuilder('j')
            ->leftJoin('j.schedule', 'schedule')
            ->andWhere('j.id = :id')
            ->andWhere('schedule.id = :scheduleId')
            ->setParameter('id', $id)
            ->setParameter('scheduleId', $scheduleId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function createQueryBuilderByScheduleId(string $scheduleId): QueryBuilder
    {
        return $this->createQueryBuilder('j')
            ->innerJoin('j.schedule', 'schedule')
            ->andWhere('j.originalJob IS NULL')
            ->andWhere('schedule.id = :scheduleId')
            ->setParameter('scheduleId', $scheduleId)
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function createQueryBuilderByOriginalJobId(string $originalJobId): QueryBuilder
    {
        return $this->createQueryBuilder('j')
            ->innerJoin('j.schedule', 'schedule')
            ->andWhere('j.originalJob = :originalJobId')
            ->setParameter('originalJobId', $originalJobId)
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByCommand(string $command, array $args = []): ?JobInterface
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.command = :command')
            ->andWhere('j.args = :args')
            ->setParameter('command', $command)
            ->setParameter('args', $args, Type::JSON_ARRAY)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findFirstOneByCommand(string $command, array $args = []): ?JobInterface
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.command = :command')
            ->andWhere('j.args = :args')
            ->orderBy('j.id', RepositoryInterface::ORDER_ASCENDING)
            ->setParameter('command', $command)
            ->setParameter('args', $args, 'json_array')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findOnePending(array $excludedIds = [], array $excludedQueues = [], array $restrictedQueues = []): ?JobInterface
    {
        $qb = $this->createQueryBuilder('j')
            ->orderBy('j.priority', 'ASC')
            ->addOrderBy('j.id', 'ASC')
        ;

        $conditions = [];

        $conditions[] = $qb->expr()->isNull('j.workerName');

        $conditions[] = $qb->expr()->lt('j.executeAfter', ':now');
        $qb->setParameter(':now', new \DateTime(), 'datetime');

        $conditions[] = $qb->expr()->eq('j.state', ':state');
        $qb->setParameter('state', JobInterface::STATE_PENDING);

        if (!empty($excludedIds)) {
            $conditions[] = $qb->expr()->notIn('j.id', ':excludedIds');
            $qb->setParameter('excludedIds', $excludedIds, Connection::PARAM_INT_ARRAY);
        }

        if (!empty($excludedQueues)) {
            $conditions[] = $qb->expr()->notIn('j.queue', ':excludedQueues');
            $qb->setParameter('excludedQueues', $excludedQueues, Connection::PARAM_STR_ARRAY);
        }

        if (!empty($restrictedQueues)) {
            $conditions[] = $qb->expr()->in('j.queue', ':restrictedQueues');
            $qb->setParameter('restrictedQueues', $restrictedQueues, Connection::PARAM_STR_ARRAY);
        }

        $qb->where(\call_user_func_array([$qb->expr(), 'andX'], $conditions));

        return $qb->getQuery()->setMaxResults(1)->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findSucceededBefore(\DateTime $retentionTime, array $excludedIds = [], $limit = 100): array
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.closedAt < :maxRetentionTime')
            ->setParameter('maxRetentionTime', $retentionTime)
            ->andWhere('j.originalJob IS NULL')
            ->andWhere('j.state = :succeeded')
            ->setParameter('succeeded', JobInterface::STATE_FINISHED)
            ->andWhere('j.id NOT IN (:excludedIds)')
            ->setParameter('excludedIds', $excludedIds)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findFinishedBefore(\DateTime $retentionTime, array $excludedIds = [], $limit = 100): array
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.closedAt < :maxRetentionTime')
            ->setParameter('maxRetentionTime', $retentionTime)
            ->andWhere('j.originalJob IS NULL')
            ->andWhere('j.id NOT IN (:excludedIds)')
            ->setParameter('excludedIds', $excludedIds)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findCancelledBefore(\DateTime $retentionTime, array $excludedIds = [], $limit = 100): array
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.state = :canceled')
            ->setParameter('canceled', JobInterface::STATE_CANCELED)
            ->andWhere('j.createdAt < :maxRetentionTime')
            ->setParameter('maxRetentionTime', $retentionTime)
            ->andWhere('j.originalJob IS NULL')
            ->andWhere('j.id NOT IN (:excludedIds)')
            ->setParameter('excludedIds', $excludedIds)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findOneStartableAndAquireLock(string $workerName, array &$excludedIds = [], $excludedQueues = [], $restrictedQueues = []): ?JobInterface
    {
        while (true) {
            $job = $this->findOnePending($excludedIds, $excludedQueues, $restrictedQueues);
            if (null === $job) {
                return null;
            }

            if ($job->isStartable() && $this->acquireLock($workerName, $job)) {
                return $job;
            }

            $excludedIds[] = $job->getId();

            // We do not want to have non-startable jobs floating around in
            // cache as they might be changed by another process. So, better
            // re-fetch them when they are not excluded anymore.
            $this->_em->detach($job);
        }

        return null;
    }

    /**
     * @param string $workerName
     * @param JobInterface $job
     *
     * @return bool
     */
    private function acquireLock(string $workerName, JobInterface $job): bool
    {
        $affectedRows = $this->_em->getConnection()->executeUpdate(
            sprintf(
                'UPDATE %s SET worker_name = :worker WHERE id = :id AND worker_name IS NULL',
                $this->_em->getClassMetadata($this->getClassName())->getTableName()
            ),
            [
                'worker' => $workerName,
                'id' => $job->getId(),
            ]
        );

        if ($affectedRows > 0) {
            $job->setWorkerName($workerName);

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function findIncomingDependencies(JobInterface $job): array
    {
        $jobIds = $this->getJobIdsOfIncomingDependencies($job);
        if (empty($jobIds)) {
            return [];
        }

        return $this->createQueryBuilder('j')
            ->leftJoin('j.dependencies', 'd')
            ->andWhere('j.id IN (:ids)')
            ->setParameter('ids', $jobIds)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param JobInterface $job
     *
     * @return array
     */
    private function getJobIdsOfIncomingDependencies(JobInterface $job): array
    {
        return $this->_em->getConnection()
            ->executeQuery('SELECT source_job_id FROM setono_sylius_scheduler_job_dependencies WHERE destination_job_id = :id', ['id' => $job->getId()])
            ->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneStale(array $excludedIds = [], ?\DateTime $maxAge = null): ?JobInterface
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.state = :running')
            ->setParameter('running', JobInterface::STATE_RUNNING)
            ->andWhere('j.workerName IS NOT NULL')
            ->andWhere('j.checkedAt < :maxAge')
            ->setParameter('maxAge', $maxAge ?: new \DateTime('-5 minutes'), 'datetime')
            ->andWhere('j.id NOT IN (:excludedIds)')
            ->setParameter('excludedIds', $excludedIds)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findStale(string $workerName): array
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.state = :running')
            ->setParameter('running', JobInterface::STATE_RUNNING)
            ->andWhere('(j.workerName = :worker OR j.workerName IS NULL)')
            ->setParameter('worker', $workerName)
            ->getQuery()
            ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findLastJobsWithError($limit = 10): array
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.state IN (:errorStates)')
            ->setParameter('errorStates', [
                JobInterface::STATE_TERMINATED,
                JobInterface::STATE_FAILED,
            ])
            ->andWhere('j.originalJob IS NULL')
            ->orderBy('j.closedAt', RepositoryInterface::ORDER_DESCENDING)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
