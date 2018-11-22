<?php

/** @noinspection ALL */

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Twig;

use Cron\CronExpression;

class CronExpressionExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('next_run_date', [$this, 'getNextRunDate']),
        ];
    }

    /**
     * @param string $cronExpression
     * @return \DateTime
     */
    public function getNextRunDate(string $cronExpression): \DateTime
    {
        return CronExpression::factory($cronExpression)->getNextRunDate();
    }

    public function getName()
    {
        return 'setono_cron_expression';
    }
}
