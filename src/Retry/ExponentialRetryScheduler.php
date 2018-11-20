<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Retry;

use Setono\SyliusSchedulerPlugin\Model\JobInterface;

class ExponentialRetryScheduler implements RetrySchedulerInterface
{
    /**
     * @var int
     */
    private $base;

    /**
     * @param int $base
     */
    public function __construct(int $base = 5)
    {
        $this->base = $base;
    }

    /**
     * @param JobInterface $originalJob
     *
     * @return \DateTime
     */
    public function scheduleNextRetry(JobInterface $originalJob): \DateTime
    {
        return new \DateTime(sprintf(
            '+%s seconds',
            $this->base ** count($originalJob->getRetryJobs())
        ));
    }
}
