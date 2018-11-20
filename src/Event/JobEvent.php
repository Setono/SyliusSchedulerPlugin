<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Event;

use Setono\SyliusSchedulerPlugin\Model\JobInterface;
use Symfony\Component\EventDispatcher\Event;

abstract class JobEvent extends Event
{
    /**
     * @var JobInterface
     */
    private $job;

    /**
     * @param JobInterface $job
     */
    public function __construct(JobInterface $job)
    {
        $this->job = $job;
    }

    /**
     * @return JobInterface
     */
    public function getJob(): JobInterface
    {
        return $this->job;
    }
}
