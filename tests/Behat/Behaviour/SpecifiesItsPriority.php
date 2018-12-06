<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusSchedulerPlugin\Behat\Behaviour;

use Behat\Mink\Element\NodeElement;

trait SpecifiesItsPriority
{
    abstract protected function getElement(string $name, array $parameters = []): NodeElement;

    /**
     * {@inheritdoc}
     */
    public function specifyPriority(?int $priority): void
    {
        $this->getElement('priority')->setValue($priority);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): ?int
    {
        return (int) $this->getElement('priority')->getValue();
    }
}
