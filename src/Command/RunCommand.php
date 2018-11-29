<?php

/** @noinspection PhpComposerExtensionStubsInspection */

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Command;

use Doctrine\ORM\EntityManager;
use Setono\SyliusSchedulerPlugin\Doctrine\ORM\JobRepository;
use Setono\SyliusSchedulerPlugin\Event\NewOutputEvent;
use Setono\SyliusSchedulerPlugin\Event\StateChangeEvent;
use Setono\SyliusSchedulerPlugin\Exception\InvalidArgumentException;
use Setono\SyliusSchedulerPlugin\JobManager\JobManager;
use Setono\SyliusSchedulerPlugin\Model\JobInterface;
use Setono\SyliusSchedulerPlugin\SetonoSyliusSchedulerPluginEvent;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class RunCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'setono:scheduler:run';

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var JobManager
     */
    private $jobManager;

    /**
     * @var JobRepository
     */
    private $jobRepository;

    /**
     * @var string
     */
    private $env;

    /**
     * @var bool
     */
    private $verbose;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var array
     */
    private $runningJobs = [];

    /**
     * @var bool
     */
    private $shouldShutdown = false;

    /**
     * @param JobManager $jobManager
     * @param JobRepository $jobRepository
     * @param EntityManager $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        JobManager $jobManager,
        JobRepository $jobRepository,
        EntityManager $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->jobManager = $jobManager;
        $this->jobRepository = $jobRepository;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;

        parent::__construct();
    }

    /**
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    protected function configure()
    {
        $this
            ->setDescription('Runs jobs from the queue.')
            ->addOption('max-runtime', 'r', InputOption::VALUE_REQUIRED, 'The maximum runtime in seconds.', '900')
            ->addOption('max-concurrent-jobs', 'j', InputOption::VALUE_REQUIRED, 'The maximum number of concurrent jobs.', '4')
            ->addOption('idle-time', null, InputOption::VALUE_REQUIRED, 'Time to sleep when the queue ran out of jobs.', '2')
            ->addOption('queue', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Restrict to one or more queues.', [])
            ->addOption('worker-name', null, InputOption::VALUE_REQUIRED, 'The name that uniquely identifies this worker process.')
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startTime = time();

        $maxRuntime = (int) $input->getOption('max-runtime');
        if ($maxRuntime <= 0) {
            throw new InvalidArgumentException('The maximum runtime must be greater than zero.');
        }

        if ($maxRuntime > 600) {
            $maxRuntime += random_int(-120, 120);
        }

        $maxJobs = (int) $input->getOption('max-concurrent-jobs');
        if ($maxJobs <= 0) {
            throw new InvalidArgumentException('The maximum number of jobs per queue must be greater than zero.');
        }

        $idleTime = (int) $input->getOption('idle-time');
        if ($idleTime <= 0) {
            throw new InvalidArgumentException('Time to sleep when idling must be greater than zero.');
        }

        $restrictedQueues = $input->getOption('queue');

        /** @var string|null $workerName */
        $workerName = $input->getOption('worker-name');
        if ($workerName === null) {
            $workerName = gethostname() . '-' . getmypid();
        } elseif (\strlen($workerName) > 50) {
            throw new \RuntimeException(sprintf(
                '"worker-name" must not be longer than 50 chars, but got "%s" (%d chars).',
                $workerName,
                \strlen($workerName)
            ));
        }

        $this->env = (string) $input->getOption('env');
        $this->verbose = (string) $input->getOption('verbose');
        $this->output = $output;
        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger(null);

        if ($this->verbose) {
            $this->output->writeln('Cleaning up stale jobs');
        }

        $this->cleanUpStaleJobs($workerName);

        $this->runJobs(
            $workerName,
            $startTime,
            $maxRuntime,
            $idleTime,
            $maxJobs,
            $restrictedQueues,
            $this->getContainer()->getParameter('setono_sylius_scheduler.queue_options_defaults'), // @todo DI
            $this->getContainer()->getParameter('setono_sylius_scheduler.queue_options') // @todo DI
        );
    }

    /**
     * @param string|null $workerName
     * @param int $startTime
     * @param int $maxRuntime
     * @param int $idleTime
     * @param int $maxJobs
     * @param array $restrictedQueues
     * @param array $queueOptionsDefaults
     * @param array $queueOptions
     */
    private function runJobs(?string $workerName, int $startTime, int $maxRuntime, int $idleTime, int $maxJobs, array $restrictedQueues, array $queueOptionsDefaults, array $queueOptions): void
    {
        $hasPcntl = \extension_loaded('pcntl');

        if ($this->verbose) {
            $this->output->writeln('Running jobs');
        }

        if ($hasPcntl) {
            $this->setupSignalHandlers();
            if ($this->verbose) {
                $this->output->writeln('Signal Handlers have been installed.');
            }
        } elseif ($this->verbose) {
            $this->output->writeln('PCNTL extension is not available. Signals cannot be processed.');
        }

        while (true) {
            if ($hasPcntl) {
                pcntl_signal_dispatch();
            }

            if ($this->shouldShutdown || time() - $startTime > $maxRuntime) {
                break;
            }

            $this->checkRunningJobs();
            $this->startJobs($workerName, $idleTime, $maxJobs, $restrictedQueues, $queueOptionsDefaults, $queueOptions);

            $waitTimeInMs = random_int(500, 1000);
            usleep($waitTimeInMs * 1000);
        }

        if ($this->verbose) {
            $this->output->writeln('Entering shutdown sequence, waiting for running jobs to terminate...');
        }

        while (!empty($this->runningJobs)) {
            sleep(5);
            $this->checkRunningJobs();
        }

        if ($this->verbose) {
            $this->output->writeln('All jobs finished, exiting.');
        }
    }

    private function setupSignalHandlers(): void
    {
        pcntl_signal(SIGTERM, function () {
            if ($this->verbose) {
                $this->output->writeln('Received SIGTERM signal.');
            }

            $this->shouldShutdown = true;
        });
    }

    /**
     * @param string|null $workerName
     * @param int $idleTime
     * @param int $maxJobs
     * @param array $restrictedQueues
     * @param array $queueOptionsDefaults
     * @param array $queueOptions
     */
    private function startJobs(?string $workerName, int $idleTime, int $maxJobs, array $restrictedQueues, array $queueOptionsDefaults, array $queueOptions): void
    {
        $excludedIds = [];
        while (count($this->runningJobs) < $maxJobs) {
            $pendingJob = $this->jobRepository->findOneStartableAndAquireLock(
                $workerName,
                $excludedIds,
                $this->getExcludedQueues($queueOptionsDefaults, $queueOptions, $maxJobs),
                $restrictedQueues
            );

            if (null === $pendingJob) {
                sleep($idleTime);

                return;
            }

            $this->startJob($pendingJob);
        }
    }

    /**
     * @param array $queueOptionsDefaults
     * @param array $queueOptions
     * @param int $maxConcurrentJobs
     *
     * @return array
     */
    private function getExcludedQueues(array $queueOptionsDefaults, array $queueOptions, int $maxConcurrentJobs): array
    {
        $excludedQueues = [];
        foreach ($this->getRunningJobsPerQueue() as $queue => $count) {
            if ($count >= $this->getMaxConcurrentJobs($queue, $queueOptionsDefaults, $queueOptions, $maxConcurrentJobs)) {
                $excludedQueues[] = $queue;
            }
        }

        return $excludedQueues;
    }

    /**
     * @param string $queue
     * @param array $queueOptionsDefaults
     * @param array $queueOptions
     * @param int $maxConcurrentJobs
     *
     * @return int
     */
    private function getMaxConcurrentJobs(string $queue, array $queueOptionsDefaults, array $queueOptions, int $maxConcurrentJobs): int
    {
        if (isset($queueOptions[$queue]['max_concurrent_jobs'])) {
            return (int) $queueOptions[$queue]['max_concurrent_jobs'];
        }

        if (isset($queueOptionsDefaults['max_concurrent_jobs'])) {
            return (int) $queueOptionsDefaults['max_concurrent_jobs'];
        }

        return $maxConcurrentJobs;
    }

    /**
     * @return array
     */
    private function getRunningJobsPerQueue(): array
    {
        $runningJobsPerQueue = [];
        foreach ($this->runningJobs as $jobDetails) {
            /** @var JobInterface $job */
            $job = $jobDetails['job'];

            $queue = $job->getQueue();
            if (!isset($runningJobsPerQueue[$queue])) {
                $runningJobsPerQueue[$queue] = 0;
            }
            ++$runningJobsPerQueue[$queue];
        }

        return $runningJobsPerQueue;
    }

    private function checkRunningJobs(): void
    {
        foreach ($this->runningJobs as $i => &$data) {
            $newOutput = substr($data['process']->getOutput(), $data['output_pointer']);
            $data['output_pointer'] += \strlen($newOutput);

            $newErrorOutput = substr($data['process']->getErrorOutput(), $data['error_output_pointer']);
            $data['error_output_pointer'] += \strlen($newErrorOutput);

            if (!empty($newOutput)) {
                $event = new NewOutputEvent($data['job'], $newOutput, NewOutputEvent::TYPE_STDOUT);
                $this->eventDispatcher->dispatch(SetonoSyliusSchedulerPluginEvent::JOB_NEW_OUTPUT, $event);
                $newOutput = $event->getNewOutput();
            }

            if (!empty($newErrorOutput)) {
                $event = new NewOutputEvent($data['job'], $newErrorOutput, NewOutputEvent::TYPE_STDERR);
                $this->eventDispatcher->dispatch(SetonoSyliusSchedulerPluginEvent::JOB_NEW_OUTPUT, $event);
                $newErrorOutput = $event->getNewOutput();
            }

            if ($this->verbose) {
                if (!empty($newOutput)) {
                    $this->output->writeln('Job ' . $data['job']->getId() . ': ' . str_replace("\n", "\nJob " . $data['job']->getId() . ': ', $newOutput));
                }

                if (!empty($newErrorOutput)) {
                    $this->output->writeln('Job ' . $data['job']->getId() . ': ' . str_replace("\n", "\nJob " . $data['job']->getId() . ': ', $newErrorOutput));
                }
            }

            // Check whether this process exceeds the maximum runtime, and terminate if that is
            // the case.
            $runtime = time() - $data['job']->getStartedAt()->getTimestamp();
            /** @noinspection NotOptimalIfConditionsInspection */
            if ($data['job']->getMaxRuntime() > 0 && $runtime > $data['job']->getMaxRuntime()) {
                $data['process']->stop(5);

                $this->output->writeln($data['job'] . ' terminated; maximum runtime exceeded.');
                $this->jobManager->closeJob($data['job'], JobInterface::STATE_TERMINATED);
                unset($this->runningJobs[$i]);

                continue;
            }

            if ($data['process']->isRunning()) {
                // For long running processes, it is nice to update the output status regularly.
                $data['job']->addOutput($newOutput);
                $data['job']->addErrorOutput($newErrorOutput);
                $data['job']->setCheckedAt(new \DateTime());

                $this->entityManager->persist($data['job']);
                $this->entityManager->flush($data['job']);

                continue;
            }

            $this->output->writeln($data['job'] . ' finished with exit code ' . $data['process']->getExitCode() . '.');

            // If the Job exited with an exception, let's reload it so that we
            // get access to the stack trace. This might be useful for listeners.
            $this->entityManager->refresh($data['job']);

            $data['job']->setExitCode($data['process']->getExitCode());
            $data['job']->setOutput($data['process']->getOutput());
            $data['job']->setErrorOutput($data['process']->getErrorOutput());
            $data['job']->setRuntime(time() - $data['start_time']);

            $newState = 0 === $data['process']->getExitCode() ? JobInterface::STATE_FINISHED : JobInterface::STATE_FAILED;
            $this->jobManager->closeJob($data['job'], $newState);
            unset($this->runningJobs[$i]);
        }
        unset($data);

        gc_collect_cycles();
    }

    /**
     * @param JobInterface $job
     */
    private function startJob(JobInterface $job): void
    {
        $event = new StateChangeEvent($job, JobInterface::STATE_RUNNING);
        $this->eventDispatcher->dispatch(SetonoSyliusSchedulerPluginEvent::JOB_STATE_CHANGED, $event);
        $newState = $event->getNewState();

        if (JobInterface::STATE_CANCELED === $newState) {
            $this->jobManager->closeJob($job, JobInterface::STATE_CANCELED);

            return;
        }

        if (JobInterface::STATE_RUNNING !== $newState) {
            throw new \LogicException(sprintf('Unsupported new state "%s".', $newState));
        }

        $job->setState(JobInterface::STATE_RUNNING);

        $this->entityManager->persist($job);
        $this->entityManager->flush($job);

        $args = $this->getBasicCommandLineArgs();
        $args[] = $job->getCommand();
        // $args[] = '--jms-job-id=' . $job->getId();

        foreach ($job->getArgs() as $arg) {
            $args[] = $arg;
        }

        $process = new Process($args);
        $process->start();
        $this->output->writeln(sprintf(
            'Started %s.',
            (string) $job
        ));

        $this->runningJobs[] = [
            'process' => $process,
            'job' => $job,
            'start_time' => time(),
            'output_pointer' => 0,
            'error_output_pointer' => 0,
        ];
    }

    /**
     * Cleans up stale jobs.
     *
     * A stale job is a job where this command has exited with an error
     * condition. Although this command is very robust, there might be cases
     * where it might be terminated abruptly (like a PHP segfault, a SIGTERM signal, etc.).
     *
     * In such an error condition, these jobs are cleaned-up on restart of this command.
     *
     * @param string $workerName
     */
    private function cleanUpStaleJobs(string $workerName): void
    {
        /** @var JobInterface[] $staleJobs */
        $staleJobs = $this->jobRepository->findStale($workerName);
        foreach ($staleJobs as $job) {
            // If the original job has retry jobs, then one of them is still in
            // running state. We can skip the original job here as it will be
            // processed automatically once the retry job is processed.
            if (!$job->isRetryJob() && count($job->getRetryJobs()) > 0) {
                continue;
            }

            $args = $this->getBasicCommandLineArgs();
            $args[] = 'setono:scheduler:mark-incomplete';
            $args[] = $job->getId();

            // We use a separate process to clean up.
            $process = new Process($args);
            if (0 !== $process->run()) {
                $ex = new ProcessFailedException($process);

                $this->output->writeln(sprintf(
                    'There was an error when marking %s as incomplete: %s',
                    $job,
                    $ex->getMessage()
                ));
            }
        }
    }

    /**
     * @return array
     */
    private function getBasicCommandLineArgs(): array
    {
        $args = [
            PHP_BINARY,
            $_SERVER['SYMFONY_CONSOLE_FILE'] ?? $_SERVER['argv'][0],
            '--env=' . $this->env,
        ];

        if ($this->verbose) {
            $args[] = '--verbose';
        }

        return $args;
    }
}
