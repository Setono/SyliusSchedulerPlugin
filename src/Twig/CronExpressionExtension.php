<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Twig;

use Cron\CronExpression;

class CronExpressionExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new \Twig_SimpleFilter('next_run_date', [$this, 'getNextRunDate']),
        ];
    }

    /**
     * @param string $cronExpression
     *
     * @return \DateTime
     */
    public function getNextRunDate(string $cronExpression): \DateTime
    {
        return CronExpression::factory($cronExpression)->getNextRunDate();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'setono_cron_expression';
    }
}
