<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;

interface JobInterface extends ResourceInterface
{
    /** State if job is inserted, but not yet ready to be started. */
    const STATE_NEW = 'new';

    /**
     * State if job is inserted, and might be started.
     *
     * It is important to note that this does not automatically mean that all
     * jobs of this state can actually be started, but you have to check
     * isStartable() to be absolutely sure.
     *
     * In contrast to NEW, jobs of this state at least might be started,
     * while jobs of state NEW never are allowed to be started.
     */
    const STATE_PENDING = 'pending';

    /** State if job was never started, and will never be started. */
    const STATE_CANCELED = 'canceled';

    /** State if job was started and has not exited, yet. */
    const STATE_RUNNING = 'running';

    /** State if job exists with a successful exit code. */
    const STATE_FINISHED = 'finished';

    /** State if job exits with a non-successful exit code. */
    const STATE_FAILED = 'failed';

    /** State if job exceeds its configured maximum runtime. */
    const STATE_TERMINATED = 'terminated';

    /**
     * State if an error occurs in the runner command.
     *
     * The runner command is the command that actually launches the individual
     * jobs. If instead an error occurs in the job command, this will result
     * in a state of FAILED.
     */
    const STATE_INCOMPLETE = 'incomplete';

    /**
     * State if an error occurs in the runner command.
     *
     * The runner command is the command that actually launches the individual
     * jobs. If instead an error occurs in the job command, this will result
     * in a state of FAILED.
     */
    const DEFAULT_QUEUE = 'default';
    const MAX_QUEUE_LENGTH = 50;

    const PRIORITY_LOW = -5;
    const PRIORITY_DEFAULT = 0;
    const PRIORITY_HIGH = 5;

    /**
     * @return string
     */
    public function getState(): string;

    /**
     * @param string $newState
     */
    public function setState(string $newState): void;

    /**
     * @param null|string $workerName
     */
    public function setWorkerName(?string $workerName): void;

    /**
     * @return null|string
     */
    public function getWorkerName(): ?string;

    /**
     * @return int
     */
    public function getPriority(): int;

    /**
     * @return bool
     */
    public function isInFinalState(): bool;

    /**
     * @return bool
     */
    public function isStartable(): bool;

    /**
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?\DateTime;

    /**
     * @return \DateTime|null
     */
    public function getClosedAt(): ?\DateTime;

    /**
     * @return \DateTime|null
     */
    public function getExecuteAfter(): ?\DateTime;

    /**
     * @param \DateTime $executeAfter
     */
    public function setExecuteAfter(?\DateTime $executeAfter): void;

    /**
     * @return string
     */
    public function getCommand(): string;

    /**
     * @param string $command
     */
    public function setCommand(string $command): void;

    /**
     * @return array
     */
    public function getArgs(): array;

    /**
     * @param array $args
     */
    public function setArgs(array $args): void;

    /**
     * @return bool
     */
    public function isClosedNonSuccessful(): bool;

    /**
     * @return Collection
     */
    public function getDependencies(): Collection;

    /**
     * @param JobInterface $job
     * @return bool
     */
    public function hasDependency(JobInterface $job): bool;

    /**
     * @param JobInterface $job
     */
    public function addDependency(JobInterface $job): void;

    /**
     * @param string $output
     */
    public function addOutput(string $output): void;

    /**
     * @param string $output
     */
    public function addErrorOutput(string $output): void;

    /**
     * @param null|string $output
     */
    public function setOutput(?string $output): void;

    /**
     * @param null|string $output
     */
    public function setErrorOutput(?string $output): void;

    /**
     * @return null|string
     */
    public function getOutput(): ?string;

    /**
     * @return null|string
     */
    public function getErrorOutput(): ?string;

    /**
     * @param int|null $code
     */
    public function setExitCode(?int $code): void;

    /**
     * @return int|null
     */
    public function getExitCode(): ?int;

    /**
     * @param int $maxRuntime
     */
    public function setMaxRuntime(int $maxRuntime): void;

    /**
     * @return int
     */
    public function getMaxRuntime(): int;

    /**
     * @return \DateTime|null
     */
    public function getStartedAt(): ?\DateTime;

    /**
     * @return int
     */
    public function getMaxRetries(): int;

    /**
     * @param int $tries
     */
    public function setMaxRetries(int $maxRetries): void;

    /**
     * @return bool
     */
    public function isRetryAllowed(): bool;

    /**
     * @return JobInterface
     */
    public function getOriginalJob(): JobInterface;

    /**
     * @param JobInterface $job
     */
    public function setOriginalJob(JobInterface $job): void;

    /**
     * @param JobInterface $job
     */
    public function addRetryJob(JobInterface $job): void;

    /**
     * @return ArrayCollection|JobInterface[]
     */
    public function getRetryJobs(): Collection;

    /**
     * @return bool
     */
    public function isRetryJob(): bool;

    /**
     * @return bool
     */
    public function isRetried(): bool;

    /**
     * @return \DateTime|null
     */
    public function getCheckedAt(): ?\DateTime;

    /**
     * @return string
     */
    public function getQueue(): string;

    /**
     * @return bool
     */
    public function isNew(): bool;

    /**
     * @return bool
     */
    public function isPending(): bool;

    /**
     * @return bool
     */
    public function isCanceled(): bool;

    /**
     * @return bool
     */
    public function isRunning(): bool;

    /**
     * @return bool
     */
    public function isTerminated(): bool;

    /**
     * @return bool
     */
    public function isFailed(): bool;

    /**
     * @return bool
     */
    public function isFinished(): bool;

    /**
     * @return bool
     */
    public function isIncomplete(): bool;
}
