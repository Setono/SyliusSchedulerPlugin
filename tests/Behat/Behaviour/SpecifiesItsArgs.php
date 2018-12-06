<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusSchedulerPlugin\Behat\Behaviour;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Behaviour\DocumentAccessor;
use Webmozart\Assert\Assert;

trait SpecifiesItsArgs
{
    use DocumentAccessor;

    /**
     * @param string $name
     * @param array $parameters
     * @return NodeElement
     */
    abstract protected function getElement(string $name, array $parameters = []): NodeElement;

    /**
     * {@inheritdoc}
     */
    public function specifyArgs(array $args): void
    {
        $this->removeArguments();
        foreach ($args as $arg) {
            $this->addArgument($arg);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addArgument(?string $arg): void
    {
        $this->clickAddArgumentButton();
        $this->getLastArgumentElement()->fillField('input', $arg);
    }

    /**
     * {@inheritdoc}
     */
    public function clickAddArgumentButton(): void
    {
        $this->getDocument()->clickLink('Add argument');
    }

    /**
     * {@inheritdoc}
     */
    public function haveArgument(string $argument): bool
    {
        return in_array($argument, $this->getArguments());
    }

    /**
     * {@inheritdoc}
     */
    public function getArguments(): array
    {
        return array_map(function(NodeElement $item): string {
            return (string)$item->find('css', 'input')->getValue();
        }, $this->findArguments());
    }

    public function removeArguments(): void
    {
        foreach ($this->findArguments() as $item) {
            $removeArgumentButton = $item->find('css', 'a[data-form-collection="delete"]');

            Assert::isInstanceOf($removeArgumentButton, NodeElement::class, 'Remove argument button not found');

            $removeArgumentButton->click();
        }
    }

    /**
     * @return NodeElement
     */
    private function getLastArgumentElement(): NodeElement
    {
        $items = $this->findArguments();

        Assert::notEmpty($items);

        return end($items);
    }

    /**
     * @return array|NodeElement[]
     */
    private function findArguments(): array
    {
        return $this->getElement('args')->findAll('css', 'div[data-form-collection="item"]');
    }
}
