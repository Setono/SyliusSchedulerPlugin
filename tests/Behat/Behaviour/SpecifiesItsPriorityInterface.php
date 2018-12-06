<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusSchedulerPlugin\Behat\Behaviour;

use Behat\Mink\Element\NodeElement;

interface SpecifiesItsPriorityInterface
{
    /**
     * @param int|null $priority
     */
    public function specifyPriority(?int $priority): void;

    /**
     * @return int|null
     */
    public function getPriority(): ?int;
}
