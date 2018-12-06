<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Factory;

use Setono\SyliusSchedulerPlugin\Model\ScheduleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface ScheduleFactoryInterface extends FactoryInterface
{
    /**
     * @param string $command
     * @param array $args
     * @param string|null $scheduleName
     * @param string|null $scheduleCode
     *
     * @return ScheduleInterface
     */
    public function createForCommand(string $command, array $args = [], ?string $scheduleName = null, ?string $scheduleCode = null): ScheduleInterface;
}
