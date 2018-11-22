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
     * @return null|string
     */
    public function getName(): ?string;

    /**
     * @param null|string $name
     */
    public function setName(?string $name): void;

    /**
     * @return null|JobInterface
     */
    public function getLatestJob(): ?JobInterface;

    /**
     * @return null|string
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
     * @param string $queue
     */
    public function setQueue(string $queue): void;

    /**
     * @return string
     */
    public function getQueue(): string;

    /**
     * @param int $priority
     */
    public function setPriority(int $priority): void;

    /**
     * @return int
     */
    public function getPriority(): int;

    /**
     * @return string
     */
    public function getCronExpression(): string;

    /**
     * @param string $cronExpression
     */
    public function setCronExpression(string $cronExpression): void;

    /**
     * @param \DateTime|null $createdAt
     */
    public function setCreatedAt(?\DateTime $createdAt): void;

    /**
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?\DateTime;

    /**
     * @param JobInterface $job
     */
    public function addJob(JobInterface $job): void;

    /**
     * @return Collection|JobInterface[]
     */
    public function getJobs(): Collection;
}