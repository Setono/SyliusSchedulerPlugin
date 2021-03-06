<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Factory;

use Setono\SyliusSchedulerPlugin\Model\JobInterface;
use Setono\SyliusSchedulerPlugin\Model\ScheduleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface JobFactoryInterface extends FactoryInterface
{
    /**
     * @param JobInterface $job
     *
     * @return JobInterface
     */
    public function createRetryJob(JobInterface $job): JobInterface;

    /**
     * @param ScheduleInterface $schedule
     * @param string|\DateTime $currentTime
     *
     * @return JobInterface
     */
    public function createFromSchedule(ScheduleInterface $schedule, $currentTime = 'now'): JobInterface;
}
