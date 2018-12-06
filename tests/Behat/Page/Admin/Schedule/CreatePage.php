<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusSchedulerPlugin\Behat\Page\Admin\Schedule;

use Sylius\Behat\Behaviour\NamesIt;
use Sylius\Behat\Behaviour\SpecifiesItsCode;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Tests\Setono\SyliusSchedulerPlugin\Behat\Behaviour\SpecifiesItsArgs;
use Tests\Setono\SyliusSchedulerPlugin\Behat\Behaviour\SpecifiesItsCommand;
use Tests\Setono\SyliusSchedulerPlugin\Behat\Behaviour\SpecifiesItsCronExpression;
use Tests\Setono\SyliusSchedulerPlugin\Behat\Behaviour\SpecifiesItsPriority;
use Tests\Setono\SyliusSchedulerPlugin\Behat\Behaviour\SpecifiesItsQueue;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use NamesIt;
    use SpecifiesItsCode;
    use SpecifiesItsCommand;
    use SpecifiesItsArgs;
    use SpecifiesItsQueue;
    use SpecifiesItsPriority;
    use SpecifiesItsCronExpression;
    use PageDefinedElements;
}
