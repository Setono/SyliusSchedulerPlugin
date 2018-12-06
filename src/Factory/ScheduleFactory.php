<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Factory;

use Setono\SyliusSchedulerPlugin\Model\ScheduleInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Resource\Factory\FactoryInterface;

class ScheduleFactory implements ScheduleFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(
        FactoryInterface $factory
    ) {
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        return $this->factory->createNew();
    }

    /**
     * {@inheritdoc}
     */
    public function createForCommand(string $command, array $args = [], ?string $scheduleName = null, ?string $scheduleCode = null): ScheduleInterface
    {
        /** @var ScheduleInterface $schedule */
        $schedule = $this->createNew();
        if (null === $scheduleName) {
            $scheduleName = $command;
        }
        if (null === $scheduleCode) {
            $scheduleCode = StringInflector::nameToCode(
                \strtolower($scheduleName)
            );
        }
        $schedule->setName($scheduleName);
        $schedule->setCode($scheduleCode);
        $schedule->setCommand($command);
        $schedule->setArgs($args);

        return $schedule;
    }
}
