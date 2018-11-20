<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Retry;

use Setono\SyliusSchedulerPlugin\Model\JobInterface;

interface RetrySchedulerInterface
{
    /**
     * Schedules the next retry of a job.
     *
     * When this method is called, it has already been decided that a retry should be attempted. The implementation
     * should needs to decide when that should happen.
     *
     * @param JobInterface $originalJob
     *
     * @return \DateTime
     */
    public function scheduleNextRetry(JobInterface $originalJob): \DateTime;
}
