<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusSchedulerPlugin\Behat\Behaviour;

interface SpecifiesItsCommandInterface
{
    /**
     * @param string|null $command
     */
    public function specifyCommand(?string $command): void;

    /**
     * @return string|null
     */
    public function getCommand(): ?string;

}
