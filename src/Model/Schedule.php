<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Schedule implements ScheduleInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string|null
     */
    private $code;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $command;

    /**
     * @var array
     */
    private $args = [];

    /**
     * @var string
     */
    private $queue = JobInterface::DEFAULT_QUEUE;

    /**
     * @var int
     */
    private $priority = JobInterface::PRIORITY_DEFAULT;

    /**
     * @var string
     */
    private $cronExpression = self::DEFAULT_CRON_EXPRESSION;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var Collection|JobInterface[]
     */
    private $jobs;

    public function __construct()
    {
        $this->jobs = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return sprintf(
            'Schedule (id = %s, command = "%s")',
            $this->id,
            $this->command
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getLatestJob(): ?JobInterface
    {
        if ($this->jobs->isEmpty()) {
            return null;
        }

        return $this->jobs->last();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommand(): ?string
    {
        return $this->command;
    }

    /**
     * {@inheritdoc}
     */
    public function setCommand(string $command): void
    {
        $this->command = $command;
    }

    /**
     * {@inheritdoc}
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * {@inheritdoc}
     */
    public function setArgs(array $args): void
    {
        $this->args = $args;
    }

    /**
     * {@inheritdoc}
     */
    public function setQueue(string $queue): void
    {
        $this->queue = $queue;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueue(): string
    {
        return $this->queue;
    }

    /**
     * {@inheritdoc}
     */
    public function setPriority(int $priority): void
    {
        $this->priority = $priority * -1;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        return $this->priority * -1;
    }

    /**
     * {@inheritdoc}
     */
    public function getCronExpression(): string
    {
        return $this->cronExpression;
    }

    /**
     * {@inheritdoc}
     */
    public function setCronExpression(string $cronExpression): void
    {
        $this->cronExpression = $cronExpression;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function addJob(JobInterface $job): void
    {
        if (!$this->jobs->contains($job)) {
            $this->jobs->add($job);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getJobs(): Collection
    {
        return $this->jobs;
    }
}
