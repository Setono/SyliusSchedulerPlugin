<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Command;

use Setono\SyliusSchedulerPlugin\Doctrine\ORM\JobRepositoryInterface;
use Setono\SyliusSchedulerPlugin\Doctrine\ORM\ScheduleRepositoryInterface;
use Setono\SyliusSchedulerPlugin\Factory\JobFactoryInterface;
use Setono\SyliusSchedulerPlugin\Model\JobInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ScheduleJobCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'setono:scheduler:schedule';

    /**
     * @var JobFactoryInterface
     */
    private $jobFactory;

    /**
     * @var JobRepositoryInterface
     */
    private $jobRepository;

    /**
     * @var ScheduleRepositoryInterface
     */
    private $scheduleRepository;

    /**
     * @param JobFactoryInterface $jobFactory
     * @param JobRepositoryInterface $jobRepository
     * @param ScheduleRepositoryInterface $scheduleRepository
     */
    public function __construct(
        JobFactoryInterface $jobFactory,
        JobRepositoryInterface $jobRepository,
        ScheduleRepositoryInterface $scheduleRepository
    ) {
        $this->jobFactory = $jobFactory;
        $this->jobRepository = $jobRepository;
        $this->scheduleRepository = $scheduleRepository;

        parent::__construct();
    }

    /**
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    protected function configure()
    {
        $this
            ->setDescription('Schedule jobs from the queue.')
            ->addOption('queue', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Restrict to one or more queues.', [])
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $restrictedQueues = $input->getOption('queue');

        $now = new \DateTime();
        $schedules = $this->scheduleRepository->findByQueues($restrictedQueues);
        foreach ($schedules as $schedule) {
            if (!$schedule->isNextJobShouldBeCreated($now)) {
                continue;
            }

            $job = $this->jobFactory->createFromSchedule($schedule);
            $job->setState(JobInterface::STATE_PENDING);
            $this->jobRepository->add($job);

            $output->writeln(sprintf(
                '%s scheduled to run at %s',
                (string) $job,
                null === $job->getExecuteAfter() ? '' : $job->getExecuteAfter()->format('Y-m-d H:i:s')
            ));
        }
    }
}
