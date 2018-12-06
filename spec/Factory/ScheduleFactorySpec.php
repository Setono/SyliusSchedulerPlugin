<?php

declare(strict_types=1);

namespace spec\Setono\SyliusSchedulerPlugin\Factory;

use PhpSpec\ObjectBehavior;
use Setono\SyliusSchedulerPlugin\Factory\ScheduleFactory;
use Setono\SyliusSchedulerPlugin\Factory\ScheduleFactoryInterface;
use Setono\SyliusSchedulerPlugin\Model\ScheduleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

class ScheduleFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $factory): void
    {
        $this->beConstructedWith($factory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ScheduleFactory::class);
    }

    function it_is_schedule_factory_instance(): void
    {
        $this->shouldImplement(ScheduleFactoryInterface::class);
    }

    function it_is_factory_instance(): void
    {
        $this->shouldImplement(FactoryInterface::class);
    }

    function it_creates_schedule(FactoryInterface $factory, ScheduleInterface $schedule): void
    {
        $this->beConstructedWith($factory);
        $factory->createNew()->willReturn($schedule);
        $this->createNew()->shouldReturn($schedule);
    }

    function it_creates_schedule_for_command(FactoryInterface $factory, ScheduleInterface $schedule): void
    {
        $this->beConstructedWith($factory);
        $factory->createNew()->willReturn($schedule);

        $schedule->setName('name')->shouldBeCalled();
        $schedule->setCode('code')->shouldBeCalled();
        $schedule->setCommand('command')->shouldBeCalled();
        $schedule->setArgs([])->shouldBeCalled();

        $this->createForCommand('command', [], 'name', 'code')->shouldReturn($schedule);
    }

}
