<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusSchedulerPlugin\Behat\Behaviour;

use Behat\Mink\Element\NodeElement;

trait SpecifiesItsQueue
{
    abstract protected function getElement(string $name, array $parameters = []): NodeElement;

    /**
     * {@inheritdoc}
     */
    public function specifyQueue(?string $queue): void
    {
        $this->getElement('queue')->setValue($queue);
    }

    /**
     * {@inheritdoc}
     */
    public function getQueue(): ?string
    {
        return $this->getElement('queue')->getValue();
    }
}
