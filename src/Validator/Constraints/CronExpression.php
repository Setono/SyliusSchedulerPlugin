<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class CronExpression extends Constraint
{
    /**
     * @var string
     */
    public $message = 'setono_sylius_scheduler.schedule.cron_expression.valid';

    /**
     * {@inheritdoc}
     */
    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy(): string
    {
        return 'setono_sylius_scheduler_schedule_cron_expression_validator';
    }
}
