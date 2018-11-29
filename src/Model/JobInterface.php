<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;

interface JobInterface extends ResourceInterface
{
    /** State if job is inserted, but not yet ready to be started. */
    public const STATE_NEW = 'new';

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
    public const STATE_PENDING = 'pending';

    /**
     * State if job was never started, and will never be started.
     */
    public const STATE_CANCELED = 'canceled';

    /**
     * State if job was started and has not exited, yet.
     */
    public const STATE_RUNNING = 'running';

    /**
     * State if job exists with a successful exit code.
     */
    public const STATE_FINISHED = 'finished';

    /**
     * State if job exits with a non-successful exit code.
     */
    public const STATE_FAILED = 'failed';

    /**
     * State if job exceeds its configured maximum runtime.
     */
    public const STATE_TERMINATED = 'terminated';

    /**
     * State if an error occurs in the runner command.
     *
     * The runner command is the command that actually launches the individual
     * jobs. If instead an error occurs in the job command, this will result
     * in a state of FAILED.
     */
    public const STATE_INCOMPLETE = 'incomplete';

    /**
     * State if an error occurs in the runner command.
     *
     * The runner command is the command that actually launches the individual
     * jobs. If instead an error occurs in the job command, this will result
     * in a state of FAILED.
     */
    public const DEFAULT_QUEUE = 'default';
    public const MAX_QUEUE_LENGTH = 50;

    public const PRIORITY_LOW = -5;
    public const PRIORITY_DEFAULT = 0;
    public const PRIORITY_HIGH = 5;

    /**
     * @return string
     */
    public function __toString();

    /**
     * @return ScheduleInterface|null
     */
    public function getSchedule(): ?ScheduleInterface;

    /**
     * @param ScheduleInterface|null $schedule
     */
    public function setSchedule(?ScheduleInterface $schedule): void;

    /**
     * @return string
     */
    public function getState(): string;

    /**
     * @param string $state
     */
    public function setState(string $state): void;

    /**
     * @param string|null $workerName
     */
    public function setWorkerName(?string $workerName): void;

    /**
     * @return string|null
     */
    public function getWorkerName(): ?string;

    /**
     * @param int $priority
     */
    public function setPriority(int $priority): void;

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
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt): void;

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime;

    /**
     * @param \DateTime|null $closedAt
     */
    public function setClosedAt(?\DateTime $closedAt): void;

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
     * @return string|null
     */
    public function getCommand(): ?string;

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
     * @return Collection|JobInterface[]
     */
    public function getDependencies(): Collection;

    /**
     * @noinspection PhpDocSignatureInspection
     *
     * @param self $job
     *
     * @return bool
     */
    public function hasDependency(self $job): bool;

    /**
     * @noinspection PhpDocSignatureInspection
     *
     * @param self $job
     */
    public function addDependency(self $job): void;

    /**
     * @param string $output
     */
    public function addOutput(string $output): void;

    /**
     * @param string $output
     */
    public function addErrorOutput(string $output): void;

    /**
     * @param string|null $output
     */
    public function setOutput(?string $output): void;

    /**
     * @param string|null $output
     */
    public function setErrorOutput(?string $output): void;

    /**
     * @return string|null
     */
    public function getOutput(): ?string;

    /**
     * @return string|null
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
     * @return int|null
     */
    public function getRuntime(): ?int;

    /**
     * @param int|null $runtime
     */
    public function setRuntime(?int $runtime): void;

    /**
     * @param \DateTime|null $startedAt
     */
    public function setStartedAt(?\DateTime $startedAt): void;

    /**
     * @return \DateTime|null
     */
    public function getStartedAt(): ?\DateTime;

    /**
     * @return int
     */
    public function getMaxRetries(): int;

    /**
     * @param int $maxRetries
     */
    public function setMaxRetries(int $maxRetries): void;

    /**
     * @return bool
     */
    public function isRetryAllowed(): bool;

    /**
     * @return self
     */
    public function getOriginalJob(): self;

    /**
     * @noinspection PhpDocSignatureInspection
     *
     * @param self|null $job
     */
    public function setOriginalJob(?self $job): void;

    /**
     * @noinspection PhpDocSignatureInspection
     *
     * @param self $job
     */
    public function addRetryJob(self $job): void;

    /**
     * @return ArrayCollection|self[]
     */
    public function getRetryJobs(): Collection;

    /**
     * @noinspection PhpDocSignatureInspection
     *
     * @param self $job
     *
     * @return bool
     */
    public function hasRetryJob(self $job): bool;

    /**
     * @return bool
     */
    public function hasRetryJobs(): bool;

    /**
     * @noinspection PhpDocSignatureInspection
     *
     * @param self $job
     */
    public function removeRetryJob(self $job): void;

    /**
     * @return bool
     */
    public function isRetryJob(): bool;

    /**
     * @return bool
     */
    public function isRetried(): bool;

    /**
     * @param \DateTime|null $checkedAt
     */
    public function setCheckedAt(?\DateTime $checkedAt): void;

    /**
     * @return \DateTime|null
     */
    public function getCheckedAt(): ?\DateTime;

    /**
     * @param string $queue
     */
    public function setQueue(string $queue): void;

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
