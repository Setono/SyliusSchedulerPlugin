<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusSchedulerPlugin\Behat\Behaviour;

use Behat\Mink\Element\NodeElement;

trait SpecifiesItsCommand
{
    abstract protected function getElement(string $name, array $parameters = []): NodeElement;

    /**
     * {@inheritdoc}
     */
    public function specifyCommand(?string $command): void
    {
        $this->getElement('command')->setValue($command);
    }

    /**
     * {@inheritdoc}
     */
    public function getCommand(): ?string
    {
        return $this->getElement('command')->getValue();
    }
}
