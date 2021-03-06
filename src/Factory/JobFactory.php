<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Factory;

use Setono\SyliusSchedulerPlugin\Model\JobInterface;
use Setono\SyliusSchedulerPlugin\Model\ScheduleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

class JobFactory implements JobFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(
        FactoryInterface $factory
    ) {
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        return $this->factory->createNew();
    }

    /**
     * {@inheritdoc}
     */
    public function createRetryJob(JobInterface $job): JobInterface
    {
        /** @var JobInterface $retryJob */
        $retryJob = $this->createNew();
        $retryJob->setState(JobInterface::STATE_PENDING);
        $retryJob->setOriginalJob($job);
        $retryJob->setSchedule($job->getSchedule());
        $retryJob->setCommand($job->getCommand());
        $retryJob->setArgs($job->getArgs());

        $retryJob->setQueue($job->getQueue());
        $retryJob->setPriority($job->getPriority());

        $retryJob->setMaxRuntime($job->getMaxRuntime());

        return $retryJob;
    }

    /**
     * {@inheritdoc}
     */
    public function createFromSchedule(ScheduleInterface $schedule, $currentTime = 'now'): JobInterface
    {
        /** @var JobInterface $job */
        $job = $this->createNew();
        $job->setSchedule($schedule);
        $job->setCommand($schedule->getCommand());
        $job->setArgs($schedule->getArgs());
        $job->setExecuteAfter($schedule->getNextRunDate($currentTime));

        return $job;
    }
}
