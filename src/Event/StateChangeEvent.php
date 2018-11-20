<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Event;

use Setono\SyliusSchedulerPlugin\Model\JobInterface;

class StateChangeEvent extends JobEvent
{
    /**
     * @var string
     */
    private $newState;

    /**
     * @param JobInterface $job
     * @param string $newState
     */
    public function __construct(JobInterface $job, string $newState)
    {
        parent::__construct($job);

        $this->newState = $newState;
    }

    /**
     * @return string
     */
    public function getNewState(): string
    {
        return $this->newState;
    }

    /**
     * @param string $state
     */
    public function setNewState(string $state): void
    {
        $this->newState = $state;
    }

    /**
     * @return string
     */
    public function getOldState(): string
    {
        return $this->getJob()->getState();
    }
}
