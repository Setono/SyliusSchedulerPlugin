<?php

namespace Setono\SyliusSchedulerPlugin\Event;

use Setono\SyliusSchedulerPlugin\Model\JobInterface;
use Symfony\Component\EventDispatcher\Event;

abstract class JobEvent extends Event
{
    private $job;

    public function __construct(JobInterface $job)
    {
        $this->job = $job;
    }

    public function getJob()
    {
        return $this->job;
    }
}