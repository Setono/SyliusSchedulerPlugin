<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusSchedulerPlugin\Behat\Behaviour;

use Behat\Mink\Element\NodeElement;

trait SpecifiesItsCronExpression
{
    abstract protected function getElement(string $name, array $parameters = []): NodeElement;

    /**
     * {@inheritdoc}
     */
    public function specifyCronExpression(?string $cronExpression): void
    {
        $this->getElement('cronExpression')->setValue($cronExpression);
    }

    /**
     * {@inheritdoc}
     */
    public function getCronExpression(): ?string
    {
        return $this->getElement('cronExpression')->getValue();
    }
}
