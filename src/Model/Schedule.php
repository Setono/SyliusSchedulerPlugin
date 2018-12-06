<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Model;

use Cron\CronExpression;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;

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
     * @var string|null
     */
    private $queue = JobInterface::DEFAULT_QUEUE;

    /**
     * @var int|null
     */
    private $priority = JobInterface::PRIORITY_DEFAULT;

    /**
     * @var string|null
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

    /**
     * @var CronExpression
     */
    private $cronExpressionParsed;

    public function __construct()
    {
        $this->jobs = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->initializeCronExpressionParsed();
    }

    private function initializeCronExpressionParsed(): void
    {
        $this->cronExpressionParsed = CronExpression::factory(
            CronExpression::isValidExpression($this->cronExpression) ? $this->cronExpression : self::DEFAULT_CRON_EXPRESSION
        );
    }

    public function onPostLoad(): void
    {
        $this->initializeCronExpressionParsed();
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
    public function isNextJobShouldBeCreated($currentTime = 'now'): bool
    {
        return $this->getNextRunDate($currentTime)->getTimestamp() > $this->getJobWithGreatestExecuteAfterTimestamp();
    }

    /**
     * {@inheritdoc}
     */
    public function getNextRunDate($currentTime = 'now'): \DateTime
    {
        return $this->cronExpressionParsed->getNextRunDate($currentTime);
    }

    /**
     * {@inheritdoc}
     */
    protected function getJobWithGreatestExecuteAfterTimestamp(): int
    {
        $latestJob = $this->getJobWithGreatestExecuteAfter();
        if (!$latestJob instanceof JobInterface) {
            return 0;
        }

        $executeAfter = $latestJob->getExecuteAfter();
        if (!$executeAfter instanceof \DateTime) {
            return 0;
        }

        return $executeAfter->getTimestamp();
    }

    /**
     * {@inheritdoc}
     */
    public function getJobWithGreatestExecuteAfter(): ?JobInterface
    {
        if ($this->jobs->isEmpty()) {
            return null;
        }

        $criteria = Criteria::create()->orderBy([
            'executeAfter' => Criteria::ASC,
        ]);

        return $this->jobs->matching($criteria)->last();
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
    public function setQueue(?string $queue): void
    {
        $this->queue = $queue;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueue(): ?string
    {
        return $this->queue;
    }

    /**
     * {@inheritdoc}
     */
    public function setPriority(?int $priority): void
    {
        $this->priority = $priority;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): ?int
    {
        return $this->priority;
    }

    /**
     * {@inheritdoc}
     */
    public function getCronExpression(): ?string
    {
        return $this->cronExpression;
    }

    /**
     * {@inheritdoc}
     */
    public function setCronExpression(?string $cronExpression): void
    {
        $this->cronExpression = $cronExpression;
        $this->cronExpressionParsed->setExpression(
            CronExpression::isValidExpression($this->cronExpression) ? $this->cronExpression : self::DEFAULT_CRON_EXPRESSION
        );
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
        if (!$this->hasJob($job)) {
            $this->jobs->add($job);
        }

        if ($this !== $job->getSchedule()) {
            $job->setSchedule($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getJobs(): Collection
    {
        return $this->jobs;
    }

    /**
     * {@inheritdoc}
     */
    public function hasJob(JobInterface $job): bool
    {
        return $this->jobs->contains($job);
    }

    /**
     * {@inheritdoc}
     */
    public function hasJobs(): bool
    {
        return !$this->jobs->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function removeJob(JobInterface $job): void
    {
        if ($this->hasJob($job)) {
            $job->setSchedule(null);
            $this->jobs->removeElement($job);
        }
    }
}
