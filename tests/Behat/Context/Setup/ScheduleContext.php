<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusSchedulerPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Setono\SyliusSchedulerPlugin\Doctrine\ORM\ScheduleRepositoryInterface;
use Setono\SyliusSchedulerPlugin\Factory\ScheduleFactoryInterface;
use Setono\SyliusSchedulerPlugin\Model\ScheduleInterface;
use Sylius\Behat\Service\SharedStorageInterface;

final class ScheduleContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ScheduleFactoryInterface
     */
    private $scheduleFactory;

    /**
     * @var ScheduleRepositoryInterface
     */
    private $scheduleRepository;

    /**
     * @var ObjectManager
     */
    private $scheduleManager;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ScheduleFactoryInterface $scheduleFactory
     * @param ScheduleRepositoryInterface $scheduleRepository
     * @param ObjectManager $scheduleManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ScheduleFactoryInterface $scheduleFactory,
        ScheduleRepositoryInterface $scheduleRepository,
        ObjectManager $scheduleManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->scheduleFactory = $scheduleFactory;
        $this->scheduleRepository = $scheduleRepository;
        $this->scheduleManager = $scheduleManager;
    }

    /**
     * @Given there is (also) a schedule for command :command
     * @Given there is (also) a schedule :scheduleName for command :command
     * @Given there is (also) a schedule :scheduleName for command :command identified by :scheduleCode code
     * @Given there is (also) a schedule :scheduleName for command :command with priority :priority
     */
    public function thereIsSchedule(string $command, array $args = [], ?string $scheduleName = null, ?string $scheduleCode = null, ?string $priority = null): void
    {
        $schedule = $this->scheduleFactory
            ->createForCommand($command, $args, $scheduleName, $scheduleCode)
        ;

        if (null !== $priority) {
            $schedule->setPriority(
                (int) $priority
            );
        }

        $this->scheduleRepository->add($schedule);
        $this->sharedStorage->set('schedule', $schedule);
    }

    /**
     * @Given /^(this schedule) has "([^\"]+)" queue$/
     */
    public function thisScheduleHasQueue(ScheduleInterface $schedule, string $queue)
    {
        $schedule->setQueue($queue);
        $this->scheduleManager->flush();
    }

    /**
     * @Given /^(this schedule) has priority "([^\"]+)"$/
     */
    public function thisScheduleHasPriority(ScheduleInterface $schedule, string $priority)
    {
        $schedule->setPriority((int)$priority);
        $this->scheduleManager->flush();
    }

    /**
     * @Given /^(this schedule) has "([^\"]+)" cron expression$/
     */
    public function thisScheduleHasCronExpression(ScheduleInterface $schedule, string $cronExpression)
    {
        $schedule->setCronExpression($cronExpression);
        $this->scheduleManager->flush();
    }

}
