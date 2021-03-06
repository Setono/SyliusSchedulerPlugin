<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Setono\SyliusSchedulerPlugin\Exception\LogicException;

class Job implements JobInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var ScheduleInterface|null
     */
    private $schedule;

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
    private $state = self::STATE_NEW;

    /**
     * @var string
     */
    private $queue = self::DEFAULT_QUEUE;

    /**
     * @var int
     */
    private $priority = self::PRIORITY_DEFAULT;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var ?\DateTime
     */
    private $startedAt;

    /**
     * @var ?\DateTime
     */
    private $checkedAt;

    /**
     * @var ?string
     */
    private $workerName;

    /**
     * @var ?\DateTime
     */
    private $executeAfter;

    /**
     * @var ArrayCollection|JobInterface[]
     */
    private $dependencies;

    /**
     * @var ?\DateTime
     */
    private $closedAt;

    /**
     * @var ?string
     */
    private $output;

    /**
     * @var ?string
     */
    private $errorOutput;

    /**
     * @var ?int
     */
    private $exitCode;

    /**
     * @var int
     */
    private $maxRuntime = 0;

    /**
     * @var ?int
     */
    private $runtime;

    /**
     * @var int
     */
    private $maxRetries = 0;

    /**
     * @var ?JobInterface
     */
    private $originalJob;

    /**
     * @var ArrayCollection|JobInterface[]
     */
    private $retryJobs;

    /**
     * @param string $state
     *
     * @return bool
     */
    public static function isNonSuccessfulFinalState(string $state): bool
    {
        return \in_array($state, [
            self::STATE_CANCELED,
            self::STATE_FAILED,
            self::STATE_INCOMPLETE,
            self::STATE_TERMINATED,
        ], true);
    }

    /**
     * @return array|string[]
     */
    public static function getStates(): array
    {
        return [
            self::STATE_NEW,
            self::STATE_PENDING,
            self::STATE_CANCELED,
            self::STATE_RUNNING,
            self::STATE_FINISHED,
            self::STATE_FAILED,
            self::STATE_TERMINATED,
            self::STATE_INCOMPLETE,
        ];
    }

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->executeAfter = new \DateTime('-1 second');
        $this->dependencies = new ArrayCollection();
        $this->retryJobs = new ArrayCollection();
    }

    public function __clone()
    {
        $this->state = self::STATE_PENDING;
        $this->createdAt = new \DateTime();
        $this->startedAt = null;
        $this->checkedAt = null;
        $this->closedAt = null;
        $this->workerName = null;
        $this->output = null;
        $this->errorOutput = null;
        $this->exitCode = null;
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
    public function getSchedule(): ?ScheduleInterface
    {
        return $this->schedule;
    }

    /**
     * {@inheritdoc}
     */
    public function setSchedule(?ScheduleInterface $schedule): void
    {
        $this->schedule = $schedule;

        if ($schedule instanceof ScheduleInterface) {
            $schedule->addJob($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * {@inheritdoc}
     */
    public function setWorkerName(?string $workerName): void
    {
        $this->workerName = $workerName;
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkerName(): ?string
    {
        return $this->workerName;
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
    public function isInFinalState(): bool
    {
        return !$this->isNew() && !$this->isPending() && !$this->isRunning();
    }

    /**
     * {@inheritdoc}
     */
    public function isStartable(): bool
    {
        foreach ($this->dependencies as $dependency) {
            if ($dependency->getState() !== self::STATE_FINISHED) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function setState(string $state): void
    {
        if ($state === $this->state) {
            return;
        }

        switch ($this->state) {
            case self::STATE_NEW:
                if (self::STATE_CANCELED === $state) {
                    $this->closedAt = new \DateTime();
                }

                break;
            case self::STATE_PENDING:
                if ($state === self::STATE_RUNNING) {
                    $this->startedAt = new \DateTime();
                    $this->checkedAt = new \DateTime();
                } elseif ($state === self::STATE_CANCELED) {
                    $this->closedAt = new \DateTime();
                }

                break;
            case self::STATE_RUNNING:
                $this->closedAt = new \DateTime();

                break;
            case self::STATE_FINISHED:
            case self::STATE_FAILED:
            case self::STATE_TERMINATED:
            case self::STATE_INCOMPLETE:
                break;
            default:
                throw new LogicException('The previous cases were exhaustive. Unknown state: ' . $this->state);
        }

        $this->state = $state;
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
    public function setClosedAt(?\DateTime $closedAt): void
    {
        $this->closedAt = $closedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getClosedAt(): ?\DateTime
    {
        return $this->closedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getExecuteAfter(): ?\DateTime
    {
        return $this->executeAfter;
    }

    /**
     * {@inheritdoc}
     */
    public function setExecuteAfter(?\DateTime $executeAfter): void
    {
        $this->executeAfter = $executeAfter;
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
    public function isClosedNonSuccessful(): bool
    {
        return self::isNonSuccessfulFinalState($this->state);
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies(): Collection
    {
        return $this->dependencies;
    }

    /**
     * {@inheritdoc}
     */
    public function hasDependency(JobInterface $job): bool
    {
        return $this->dependencies->contains($job);
    }

    /**
     * {@inheritdoc}
     */
    public function addDependency(JobInterface $job): void
    {
        if ($this->dependencies->contains($job)) {
            return;
        }

        if ($this->mightHaveStarted()) {
            throw new \LogicException('You cannot add dependencies to a job which might have been started already.');
        }

        $this->dependencies->add($job);
    }

    /**
     * {@inheritdoc}
     */
    public function addOutput(string $output): void
    {
        $this->output .= $output;
    }

    /**
     * {@inheritdoc}
     */
    public function addErrorOutput(string $output): void
    {
        $this->errorOutput .= $output;
    }

    /**
     * {@inheritdoc}
     */
    public function setOutput(?string $output): void
    {
        $this->output = $output;
    }

    /**
     * {@inheritdoc}
     */
    public function setErrorOutput(?string $output): void
    {
        $this->errorOutput = $output;
    }

    /**
     * {@inheritdoc}
     */
    public function getOutput(): ?string
    {
        return $this->output;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorOutput(): ?string
    {
        return $this->errorOutput;
    }

    /**
     * {@inheritdoc}
     */
    public function setExitCode(?int $code): void
    {
        $this->exitCode = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getExitCode(): ?int
    {
        return $this->exitCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setMaxRuntime(int $maxRuntime): void
    {
        $this->maxRuntime = $maxRuntime;
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxRuntime(): int
    {
        return $this->maxRuntime;
    }

    /**
     * {@inheritdoc}
     */
    public function getRuntime(): ?int
    {
        return $this->runtime;
    }

    /**
     * {@inheritdoc}
     */
    public function setRuntime(?int $runtime): void
    {
        $this->runtime = $runtime;
    }

    /**
     * {@inheritdoc}
     */
    public function setStartedAt(?\DateTime $startedAt): void
    {
        $this->startedAt = $startedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getStartedAt(): ?\DateTime
    {
        return $this->startedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxRetries(): int
    {
        return $this->maxRetries;
    }

    /**
     * {@inheritdoc}
     */
    public function setMaxRetries(int $maxRetries): void
    {
        $this->maxRetries = $maxRetries;
    }

    /**
     * {@inheritdoc}
     */
    public function isRetryAllowed(): bool
    {
        // If no retries are allowed, we can bail out directly, and we
        // do not need to initialize the retryJobs relation.
        if (0 === $this->maxRetries) {
            return false;
        }

        return count($this->retryJobs) < $this->maxRetries;
    }

    /**
     * {@inheritdoc}
     */
    public function getOriginalJob(): JobInterface
    {
        if (null === $this->originalJob) {
            return $this;
        }

        return $this->originalJob;
    }

    /**
     * {@inheritdoc}
     */
    public function setOriginalJob(?JobInterface $job): void
    {
        $this->originalJob = $job;

        if ($job instanceof JobInterface) {
            $job->addRetryJob($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addRetryJob(JobInterface $job): void
    {
        if (!$this->hasRetryJob($job)) {
            $this->retryJobs->add($job);
        }

        if ($this !== $job->getOriginalJob()) {
            $job->setOriginalJob($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRetryJobs(): Collection
    {
        return $this->retryJobs;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRetryJob(JobInterface $job): bool
    {
        return $this->retryJobs->contains($job);
    }

    /**
     * {@inheritdoc}
     */
    public function hasRetryJobs(): bool
    {
        return !$this->retryJobs->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function removeRetryJob(JobInterface $job): void
    {
        if ($this->hasRetryJob($job)) {
            $job->setOriginalJob(null);
            $this->retryJobs->removeElement($job);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isRetryJob(): bool
    {
        return null !== $this->originalJob;
    }

    /**
     * {@inheritdoc}
     */
    public function isRetried(): bool
    {
        foreach ($this->retryJobs as $job) {
            if (!$job->isInFinalState()) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setCheckedAt(?\DateTime $checkedAt): void
    {
        $this->checkedAt = $checkedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getCheckedAt(): ?\DateTime
    {
        return $this->checkedAt;
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
    public function isNew(): bool
    {
        return self::STATE_NEW === $this->state;
    }

    /**
     * {@inheritdoc}
     */
    public function isPending(): bool
    {
        return self::STATE_PENDING === $this->state;
    }

    /**
     * {@inheritdoc}
     */
    public function isCanceled(): bool
    {
        return self::STATE_CANCELED === $this->state;
    }

    /**
     * {@inheritdoc}
     */
    public function isRunning(): bool
    {
        return self::STATE_RUNNING === $this->state;
    }

    /**
     * {@inheritdoc}
     */
    public function isTerminated(): bool
    {
        return self::STATE_TERMINATED === $this->state;
    }

    /**
     * {@inheritdoc}
     */
    public function isFailed(): bool
    {
        return self::STATE_FAILED === $this->state;
    }

    /**
     * {@inheritdoc}
     */
    public function isFinished(): bool
    {
        return self::STATE_FINISHED === $this->state;
    }

    /**
     * {@inheritdoc}
     */
    public function isIncomplete(): bool
    {
        return self::STATE_INCOMPLETE === $this->state;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return sprintf(
            'Job(id = %s, command = "%s")',
            $this->id,
            $this->command
        );
    }

    /**
     * @return bool
     */
    private function mightHaveStarted(): bool
    {
        if (null === $this->id) {
            return false;
        }

        if (self::STATE_NEW === $this->state) {
            return false;
        }

        if (self::STATE_PENDING === $this->state && !$this->isStartable()) {
            return false;
        }

        return true;
    }
}
