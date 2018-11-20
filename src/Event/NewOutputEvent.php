<?php

namespace Setono\SyliusSchedulerPlugin\Event;

use Setono\SyliusSchedulerPlugin\Model\JobInterface;

class NewOutputEvent extends JobEvent
{
    const TYPE_STDOUT = 1;
    const TYPE_STDERR = 2;

    private $newOutput;
    private $type;

    public function __construct(JobInterface $job, $newOutput, $type = self::TYPE_STDOUT)
    {
        parent::__construct($job);

        $this->newOutput = $newOutput;
        $this->type = $type;
    }

    public function getNewOutput()
    {
        return $this->newOutput;
    }

    public function setNewOutput($output)
    {
        $this->newOutput = $output;
    }

    public function getType()
    {
        return $this->type;
    }
}