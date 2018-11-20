<?php

namespace Setono\SyliusSchedulerPlugin\Event;

use Setono\SyliusSchedulerPlugin\Model\JobInterface;

class StateChangeEvent extends JobEvent
{
    private $newState;

    public function __construct(JobInterface $job, $newState)
    {
        parent::__construct($job);

        $this->newState = $newState;
    }

    public function getNewState()
    {
        return $this->newState;
    }

    public function setNewState($state)
    {
        $this->newState = $state;
    }

    public function getOldState()
    {
        return $this->getJob()->getState();
    }
}