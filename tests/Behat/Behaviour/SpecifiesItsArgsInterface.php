<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusSchedulerPlugin\Behat\Behaviour;

interface SpecifiesItsArgsInterface
{
    /**
     * @param array $args
     */
    public function specifyArgs(array $args): void;

    /**
     * @param string $arg
     */
    public function addArgument(?string $arg): void;

    /**
     * @param string|null $arg
     */
    public function clickAddArgumentButton(): void;

    /**
     * @param string $argument
     * @return bool
     */
    public function haveArgument(string $argument): bool;

    /**
     * @return array
     */
    public function getArguments(): array;

    public function removeArguments(): void;

}
