<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Command;

use Doctrine\ORM\EntityManager;
use Setono\SyliusSchedulerPlugin\Doctrine\ORM\JobRepository;
use Setono\SyliusSchedulerPlugin\JobManager\JobManager;
use Setono\SyliusSchedulerPlugin\Model\JobInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CleanUpCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'setono:scheduler:clean-up';

    /**
     * @var JobManager
     */
    private $jobManager;

    /**
     * @var JobRepository
     */
    private $jobRepository;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param JobManager $jobManager
     * @param JobRepository $jobRepository
     * @param EntityManager $entityManager
     */
    public function __construct(
        JobManager $jobManager,
        JobRepository $jobRepository,
        EntityManager $entityManager
    ) {
        parent::__construct();

        $this->jobManager = $jobManager;
        $this->jobRepository = $jobRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    protected function configure()
    {
        $this
            ->setDescription('Cleans up jobs which exceed the maximum retention time.')
            ->addOption('max-retention', null, InputOption::VALUE_REQUIRED, 'The maximum retention time (value must be parsable by DateTime).', '7 days')
            ->addOption('max-retention-succeeded', null, InputOption::VALUE_REQUIRED, 'The maximum retention time for succeeded jobs (value must be parsable by DateTime).', '1 hour')
            ->addOption('per-call', null, InputOption::VALUE_REQUIRED, 'The maximum number of jobs to clean-up per call.', '1000')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->removeExpiredJobs($input);
        $this->closeStaleJobs();
    }

    /**
     * {@inheritdoc}
     */
    private function closeStaleJobs(): void
    {
        foreach ($this->findStaleJobs() as $job) {
            if ($job->isRetried()) {
                continue;
            }

            $this->jobManager->closeJob($job, JobInterface::STATE_INCOMPLETE);
        }
    }

    /**
     * @return \Generator|JobInterface[]
     */
    private function findStaleJobs(): \Generator
    {
        $excludedIds = [-1];

        do {
            $this->entityManager->clear();

            /** @var JobInterface $job */
            $job = $this->jobRepository->findOneStale($excludedIds);

            if ($job !== null) {
                $excludedIds[] = $job->getId();

                yield $job;
            }
        } while ($job !== null);
    }

    /**
     * @param InputInterface $input
     */
    private function removeExpiredJobs(InputInterface $input): void
    {
        $perCall = (int) $input->getOption('per-call');

        $connection = $this->entityManager->getConnection();
        $incomingDepsSql = $connection->getDatabasePlatform()
            ->modifyLimitQuery('SELECT 1 FROM setono_sylius_scheduler_job_dependencies WHERE destination_job_id = :id', 1)
        ;

        $count = 0;
        foreach ($this->findExpiredJobs($input) as $job) {
            ++$count;

            $result = $connection->executeQuery($incomingDepsSql, ['id' => $job->getId()]);
            if ($result->fetchColumn() !== false) {
                $this->entityManager->transactional(function () use ($job) {
                    $this->resolveDependencies($job);
                    $this->entityManager->remove($job);
                });

                continue;
            }

            $this->entityManager->remove($job);

            if ($count >= $perCall) {
                break;
            }
        }

        $this->entityManager->flush();
    }

    /**
     * @param JobInterface $job
     */
    private function resolveDependencies(JobInterface $job): void
    {
        // If this job has failed, or has otherwise not succeeded, we need to set the
        // incoming dependencies to failed if that has not been done already.
        if (!$job->isFinished()) {
            /** @var JobInterface $incomingDependency */
            foreach ($this->jobRepository->findIncomingDependencies($job) as $incomingDependency) {
                if ($incomingDependency->isInFinalState()) {
                    continue;
                }

                $finalState = JobInterface::STATE_CANCELED;
                /** @noinspection DisconnectedForeachInstructionInspection */
                if ($job->isRunning()) {
                    $finalState = JobInterface::STATE_FAILED;
                }

                $this->jobManager->closeJob($incomingDependency, $finalState);
            }
        }

        $this->entityManager->getConnection()
            ->executeUpdate(
                'DELETE FROM setono_sylius_scheduler_job_dependencies WHERE destination_job_id = :id',
                ['id' => $job->getId()]
            );
    }

    /**
     * @param InputInterface $input
     *
     * @return \Generator|JobInterface[]
     */
    private function findExpiredJobs(InputInterface $input): \Generator
    {
        $succeededJobs = function (array $excludedIds) use ($input) {
            return $this->jobRepository->findSucceededBefore(
                new \DateTime(sprintf(
                    '-%s',
                    $input->getOption('max-retention-succeeded')
                )),
                $excludedIds
            );
        };
        foreach ($this->whileResults($succeededJobs) as $job) {
            yield $job;
        }

        $finishedJobs = function (array $excludedIds) use ($input) {
            return $this->jobRepository->findFinishedBefore(
                new \DateTime(sprintf(
                    '-%s',
                    $input->getOption('max-retention')
                )),
                $excludedIds
            );
        };
        foreach ($this->whileResults($finishedJobs) as $job) {
            yield $job;
        }

        $canceledJobs = function (array $excludedIds) use ($input) {
            return $this->jobRepository->findCancelledBefore(
                new \DateTime(sprintf(
                    '-%s',
                    $input->getOption('max-retention')
                )),
                $excludedIds
            );
        };
        foreach ($this->whileResults($canceledJobs) as $job) {
            yield $job;
        }
    }

    /**
     * @param callable $resultProducer
     *
     * @return \Generator|JobInterface[]
     */
    private function whileResults(callable $resultProducer): \Generator
    {
        $excludedIds = [-1];
        do {
            /** @var JobInterface[] $jobs */
            $jobs = $resultProducer($excludedIds);
            foreach ($jobs as $job) {
                $excludedIds[] = $job->getId();
                yield $job;
            }
        } while (!empty($jobs));
    }
}
