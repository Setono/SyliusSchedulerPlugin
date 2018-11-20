<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Factory;

use Setono\SyliusSchedulerPlugin\Model\JobInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface JobFactoryInterface extends FactoryInterface
{
    /**
     * @param string $command
     * @param array $args
     * @return JobInterface
     */
    public function createForCommand(string $command, array $args = array()): JobInterface;
}