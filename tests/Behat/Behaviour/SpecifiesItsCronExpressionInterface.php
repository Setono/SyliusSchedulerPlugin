<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusSchedulerPlugin\Behat\Behaviour;

interface SpecifiesItsCronExpressionInterface
{
    /**
     * @param string|null $cronExpression
     */
    public function specifyCronExpression(?string $cronExpression): void;

    /**
     * @return string|null
     */
    public function getCronExpression(): ?string;
}
