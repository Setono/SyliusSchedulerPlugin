<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Factory;

use Setono\SyliusSchedulerPlugin\Model\JobInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

class JobFactory implements JobFactoryInterface
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
    public function createForCommand(string $command, array $args = array()): JobInterface
    {
        /** @var JobInterface $job */
        $job = $this->createNew();
        $job->setCommand($command);
        $job->setArgs($args);

        return $job;
    }
}
