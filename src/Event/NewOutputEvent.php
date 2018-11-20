<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Event;

use Setono\SyliusSchedulerPlugin\Model\JobInterface;

class NewOutputEvent extends JobEvent
{
    public const TYPE_STDOUT = 1;
    public const TYPE_STDERR = 2;

    /**
     * @var string
     */
    private $newOutput;

    /**
     * @var int
     */
    private $type;

    /**
     * @param JobInterface $job
     * @param string $newOutput
     * @param int $type
     */
    public function __construct(JobInterface $job, string $newOutput, int $type = self::TYPE_STDOUT)
    {
        parent::__construct($job);

        $this->newOutput = $newOutput;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getNewOutput(): string
    {
        return $this->newOutput;
    }

    /**
     * @param string $output
     */
    public function setNewOutput(string $output): void
    {
        $this->newOutput = $output;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }
}
