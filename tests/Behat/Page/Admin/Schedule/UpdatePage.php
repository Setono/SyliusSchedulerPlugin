<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusSchedulerPlugin\Behat\Page\Admin\Schedule;

use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Behaviour\NamesIt;
use Sylius\Behat\Behaviour\SpecifiesItsCode;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Tests\Setono\SyliusSchedulerPlugin\Behat\Behaviour\SpecifiesItsArgs;
use Tests\Setono\SyliusSchedulerPlugin\Behat\Behaviour\SpecifiesItsCommand;
use Tests\Setono\SyliusSchedulerPlugin\Behat\Behaviour\SpecifiesItsCronExpression;
use Tests\Setono\SyliusSchedulerPlugin\Behat\Behaviour\SpecifiesItsPriority;
use Tests\Setono\SyliusSchedulerPlugin\Behat\Behaviour\SpecifiesItsQueue;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use NamesIt;
    use SpecifiesItsCode;
    use ChecksCodeImmutability;
    use SpecifiesItsCommand;
    use SpecifiesItsArgs;
    use SpecifiesItsQueue;
    use SpecifiesItsPriority;
    use SpecifiesItsCronExpression;
    use PageDefinedElements;

    /**
     * {@inheritdoc}
     */
    protected function getCodeElement()
    {
        return $this->getElement('code');
    }
}
