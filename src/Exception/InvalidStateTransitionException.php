<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Exception;

use Setono\SyliusSchedulerPlugin\Model\JobInterface;

class InvalidStateTransitionException extends \InvalidArgumentException
{
    /**
     * @var JobInterface
     */
    private $job;

    /**
     * @var string
     */
    private $newState;

    /**
     * @var array
     */
    private $allowedStates;

    public function __construct(JobInterface $job, string $newState, array $allowedStates = [])
    {
        $msg = sprintf('The Job(id = %d) cannot change from "%s" to "%s". Allowed transitions: ', $job->getId(), $job->getState(), $newState);
        $msg .= count($allowedStates) > 0 ? '"' . implode('", "', $allowedStates) . '"' : '#none#';
        parent::__construct($msg);

        $this->job = $job;
        $this->newState = $newState;
        $this->allowedStates = $allowedStates;
    }

    /**
     * @return JobInterface
     */
    public function getJob(): JobInterface
    {
        return $this->job;
    }

    /**
     * @return string
     */
    public function getNewState(): string
    {
        return $this->newState;
    }

    /**
     * @return array
     */
    public function getAllowedStates(): array
    {
        return $this->allowedStates;
    }
}
