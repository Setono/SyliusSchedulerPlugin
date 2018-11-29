<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\JobManager;

use Setono\SyliusSchedulerPlugin\Model\JobInterface;

interface JobManagerInterface
{
    /**
     * @param JobInterface $job
     * @param string $finalState
     */
    public function closeJob(JobInterface $job, string $finalState): void;
}
