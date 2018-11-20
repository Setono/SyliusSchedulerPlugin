<?php

namespace Setono\SyliusSchedulerPlugin\Exception;

use Setono\SyliusSchedulerPlugin\Model\JobInterface;

class InvalidStateTransitionException extends \InvalidArgumentException
{
    private $job;
    private $newState;
    private $allowedStates;

    public function __construct(JobInterface $job, $newState, array $allowedStates = array())
    {
        $msg = sprintf('The Job(id = %d) cannot change from "%s" to "%s". Allowed transitions: ', $job->getId(), $job->getState(), $newState);
        $msg .= count($allowedStates) > 0 ? '"'.implode('", "', $allowedStates).'"' : '#none#';
        parent::__construct($msg);

        $this->job = $job;
        $this->newState = $newState;
        $this->allowedStates = $allowedStates;
    }

    public function getJob()
    {
        return $this->job;
    }

    public function getNewState()
    {
        return $this->newState;
    }

    public function getAllowedStates()
    {
        return $this->allowedStates;
    }
}