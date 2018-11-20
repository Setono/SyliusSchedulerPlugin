<?php

namespace Setono\SyliusSchedulerPlugin\JobManager;

use Setono\SyliusSchedulerPlugin\Model\JobInterface;

interface JobManagerInterface
{
    /**
     * @param $command
     * @param array $args
     * @return null|JobInterface
     */
    public function getJob($command, array $args = array()): ?JobInterface;

    /**
     * @param string $command
     * @param array $args
     * @return JobInterface
     */
    public function getOrCreateIfNotExists(string $command, array $args = array()): JobInterface;

    /**
     * @param JobInterface $job
     * @param string $finalState
     */
    public function closeJob(JobInterface $job, string $finalState): void;
}
