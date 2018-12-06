<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusSchedulerPlugin\Behat\Page\Admin\Schedule;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;
use Tests\Setono\SyliusSchedulerPlugin\Behat\Behaviour\SpecifiesItsArgsInterface;
use Tests\Setono\SyliusSchedulerPlugin\Behat\Behaviour\SpecifiesItsCommandInterface;
use Tests\Setono\SyliusSchedulerPlugin\Behat\Behaviour\SpecifiesItsCronExpressionInterface;
use Tests\Setono\SyliusSchedulerPlugin\Behat\Behaviour\SpecifiesItsPriorityInterface;
use Tests\Setono\SyliusSchedulerPlugin\Behat\Behaviour\SpecifiesItsQueueInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface,
    SpecifiesItsArgsInterface,
    SpecifiesItsCommandInterface,
    SpecifiesItsCronExpressionInterface,
    SpecifiesItsPriorityInterface,
    SpecifiesItsQueueInterface
{
    /**
     * @return bool
     */
    public function isCodeDisabled();

    /**
     * @param string $code
     */
    public function specifyCode($code);

    /**
     * @param string $name
     */
    public function nameIt($name);
}
