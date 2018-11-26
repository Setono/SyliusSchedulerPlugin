<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Factory;

use Setono\SyliusSchedulerPlugin\Model\JobInterface;
use Setono\SyliusSchedulerPlugin\Model\ScheduleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface JobFactoryInterface extends FactoryInterface
{
    /**
     * @param string $command
     * @param array $args
     *
     * @return JobInterface
     */
    public function createForCommand(string $command, array $args = []): JobInterface;

    /**
     * @param JobInterface $job
     *
     * @return JobInterface
     */
    public function createRetryJob(JobInterface $job): JobInterface;

    /**
     * @param ScheduleInterface $schedule
     *
     * @return JobInterface
     */
    public function createFromSchedule(ScheduleInterface $schedule): JobInterface;
}
