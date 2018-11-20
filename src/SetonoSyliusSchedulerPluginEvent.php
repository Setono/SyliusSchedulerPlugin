<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin;

final class SetonoSyliusSchedulerPluginEvent
{
    public const JOB_NEW_OUTPUT = 'setono_sylius_scheduler.job.new_output';
    public const JOB_STATE_CHANGED = 'setono_sylius_scheduler.job.state_changed';
}
