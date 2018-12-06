<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface ScheduleInterface extends ResourceInterface, CodeAwareInterface
{
    public const DEFAULT_CRON_EXPRESSION = '* * * * *';

    /**
     * @return string
     */
    public function __toString();

    /**
     * @param string|\DateTime $currentTime
     *
     * @return bool
     */
    public function isNextJobShouldBeCreated($currentTime = 'now'): bool;

    /**
     * @param string|\DateTime $currentTime
     *
     * @return \DateTime
     */
    public function getNextRunDate($currentTime = 'now'): \DateTime;

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void;

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
     * @param string|null $queue
     */
    public function setQueue(?string $queue): void;

    /**
     * @return string|null
     */
    public function getQueue(): ?string;

    /**
     * @param int|null $priority
     */
    public function setPriority(?int $priority): void;

    /**
     * @return int|null
     */
    public function getPriority(): ?int;

    /**
     * @return string|null
     */
    public function getCronExpression(): ?string;

    /**
     * @param string|null $cronExpression
     */
    public function setCronExpression(?string $cronExpression): void;

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt): void;

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime;

    /**
     * @param JobInterface $job
     */
    public function addJob(JobInterface $job): void;

    /**
     * @param JobInterface $job
     *
     * @return bool
     */
    public function hasJob(JobInterface $job): bool;

    /**
     * @return Collection|JobInterface[]
     */
    public function getJobs(): Collection;

    /**
     * @return JobInterface|null
     */
    public function getJobWithGreatestExecuteAfter(): ?JobInterface;

    /**
     * @return bool
     */
    public function hasJobs(): bool;

    /**
     * @param JobInterface $job
     */
    public function removeJob(JobInterface $job): void;
}
