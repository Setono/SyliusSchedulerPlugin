<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\JobManager;

use Doctrine\ORM\EntityManager;
use Setono\SyliusSchedulerPlugin\Doctrine\ORM\JobRepositoryInterface;
use Setono\SyliusSchedulerPlugin\Event\StateChangeEvent;
use Setono\SyliusSchedulerPlugin\Factory\JobFactoryInterface;
use Setono\SyliusSchedulerPlugin\Model\JobInterface;
use Setono\SyliusSchedulerPlugin\Retry\RetrySchedulerInterface;
use Setono\SyliusSchedulerPlugin\SetonoSyliusSchedulerPluginEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class JobManager
{
    /**
     * @var JobRepositoryInterface
     */
    private $jobRepository;

    /**
     * @var JobFactoryInterface
     */
    private $jobFactory;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var RetrySchedulerInterface
     */
    private $retryScheduler;

    /**
     * @param JobRepositoryInterface $jobRepository
     * @param JobFactoryInterface $jobFactory
     * @param EntityManager $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param RetrySchedulerInterface $retryScheduler
     */
    public function __construct(
        JobRepositoryInterface $jobRepository,
        JobFactoryInterface $jobFactory,
        EntityManager $entityManager,
        EventDispatcherInterface $eventDispatcher,
        RetrySchedulerInterface $retryScheduler
    ) {
        $this->jobRepository = $jobRepository;
        $this->jobFactory = $jobFactory;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->retryScheduler = $retryScheduler;
    }

    /**
     * @param JobInterface $job
     * @param string $finalState
     */
    public function closeJob(JobInterface $job, string $finalState): void
    {
        $this->entityManager->getConnection()->beginTransaction();

        try {
            $visitedJobs = [];
            $this->closeJobInternal($job, $finalState, $visitedJobs);
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();

            // Clean-up entity manager to allow for garbage collection to kick in.
            foreach ($visitedJobs as $visitedJob) {
                // If the job is an original job which is now being retried, let's
                // not remove it just yet.
                if (!$visitedJob->isClosedNonSuccessful() || $visitedJob->isRetryJob()) {
                    continue;
                }

                $this->entityManager->detach($visitedJob);
            }
        } catch (\Exception $ex) {
            $this->entityManager->getConnection()->rollBack();

            throw $ex;
        }
    }

    /**
     * @param JobInterface $job
     * @param string $finalState
     * @param array $visited
     */
    private function closeJobInternal(JobInterface $job, string $finalState, array &$visited = []): void
    {
        if (\in_array($job, $visited, true)) {
            return;
        }
        $visited[] = $job;

        if ($job->isInFinalState()) {
            return;
        }

        if (null !== $this->eventDispatcher && ($job->isRetryJob() || 0 === count($job->getRetryJobs()))) {
            $event = new StateChangeEvent($job, $finalState);
            $this->eventDispatcher->dispatch(SetonoSyliusSchedulerPluginEvent::JOB_STATE_CHANGED, $event);
            $finalState = $event->getNewState();
        }

        switch ($finalState) {
            case JobInterface::STATE_CANCELED:
                $job->setState(JobInterface::STATE_CANCELED);
                $this->entityManager->persist($job);

                if ($job->isRetryJob()) {
                    $this->closeJobInternal($job->getOriginalJob(), JobInterface::STATE_CANCELED, $visited);

                    return;
                }

                foreach ($this->jobRepository->findIncomingDependencies($job) as $dep) {
                    $this->closeJobInternal($dep, JobInterface::STATE_CANCELED, $visited);
                }

                return;
            case JobInterface::STATE_FAILED:
            case JobInterface::STATE_TERMINATED:
            case JobInterface::STATE_INCOMPLETE:
                if ($job->isRetryJob()) {
                    $job->setState($finalState);
                    $this->entityManager->persist($job);

                    $this->closeJobInternal($job->getOriginalJob(), $finalState);

                    return;
                }

                // The original job has failed, and we are allowed to retry it.
                if ($job->isRetryAllowed()) {
                    $retryJob = $this->jobFactory->createRetryJob($job);
                    $retryJob->setExecuteAfter(
                        $this->retryScheduler->scheduleNextRetry($job)
                    );

                    $job->addRetryJob($retryJob);
                    $this->entityManager->persist($retryJob);
                    $this->entityManager->persist($job);

                    return;
                }

                $job->setState($finalState);
                $this->entityManager->persist($job);

                // The original job has failed, and no retries are allowed.
                /** @var JobInterface $dep */
                foreach ($this->jobRepository->findIncomingDependencies($job) as $dep) {
                    // This is a safe-guard to avoid blowing up if there is a database inconsistency.
                    if (!$dep->isPending() && !$dep->isNew()) {
                        continue;
                    }

                    $this->closeJobInternal($dep, JobInterface::STATE_CANCELED, $visited);
                }

                return;
            case JobInterface::STATE_FINISHED:
                if ($job->isRetryJob()) {
                    $job->getOriginalJob()->setState($finalState);
                    $this->entityManager->persist(
                        $job->getOriginalJob()
                    );
                }
                $job->setState($finalState);
                $this->entityManager->persist($job);

                return;
            default:
                throw new \LogicException(sprintf(
                    'Non allowed state "%s" in closeJobInternal().',
                    $finalState
                ));
        }
    }
}
