<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusSchedulerPlugin\Behat\Page\Admin\Schedule;

trait PageDefinedElements
{
    /**
     * @return array
     */
    protected function getDefinedElements(): array
    {
        return [
            'code' => '#setono_sylius_scheduler_schedule_code',
            'name' => '#setono_sylius_scheduler_schedule_name',

            'command' => '#setono_sylius_scheduler_schedule_command',
            'args' => '#setono_sylius_scheduler_schedule_args',

            'queue' => '#setono_sylius_scheduler_schedule_queue',
            'priority' => '#setono_sylius_scheduler_schedule_priority',
            'cronExpression' => '#setono_sylius_scheduler_schedule_cronExpression',
        ];
    }
}
