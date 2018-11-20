<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\JobManager;

use Setono\SyliusSchedulerPlugin\Model\JobInterface;

interface JobManagerInterface
{
    /**
     * @param string $command
     * @param array $args
     *
     * @return JobInterface|null
     */
    public function getJob(string $command, array $args = []): ?JobInterface;

    /**
     * @param string $command
     * @param array $args
     *
     * @return JobInterface
     */
    public function getOrCreateIfNotExists(string $command, array $args = []): JobInterface;

    /**
     * @param JobInterface $job
     * @param string $finalState
     */
    public function closeJob(JobInterface $job, string $finalState): void;
}
