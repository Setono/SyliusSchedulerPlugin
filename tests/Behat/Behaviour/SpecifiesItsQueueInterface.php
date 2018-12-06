<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusSchedulerPlugin\Behat\Behaviour;

interface SpecifiesItsQueueInterface
{
    /**
     * @param string|null $queue
     */
    public function specifyQueue(?string $queue): void;

    /**
     * @return string|null
     */
    public function getQueue(): ?string;
}
