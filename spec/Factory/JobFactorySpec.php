<?php

declare(strict_types=1);

namespace spec\Setono\SyliusSchedulerPlugin\Factory;

use PhpSpec\ObjectBehavior;
use Setono\SyliusSchedulerPlugin\Factory\JobFactory;
use Setono\SyliusSchedulerPlugin\Factory\JobFactoryInterface;
use Setono\SyliusSchedulerPlugin\Model\JobInterface;
use Setono\SyliusSchedulerPlugin\Model\ScheduleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

class JobFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $factory): void
    {
        $this->beConstructedWith($factory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(JobFactory::class);
    }

    function it_is_job_factory_instance(): void
    {
        $this->shouldImplement(JobFactoryInterface::class);
    }

    function it_is_factory_instance(): void
    {
        $this->shouldImplement(FactoryInterface::class);
    }

    function it_creates_job(FactoryInterface $factory, JobInterface $job): void
    {
        $this->beConstructedWith($factory);
        $factory->createNew()->willReturn($job);
        $this->createNew()->shouldReturn($job);
    }

    function it_creates_retry_job(ScheduleInterface $schedule, FactoryInterface $factory, JobInterface $job, JobInterface $retryJob): void
    {
        $this->beConstructedWith($factory);
        $factory->createNew()->willReturn($retryJob);

        $job->getStartedAt()->willReturn(JobInterface::STATE_PENDING);
        $retryJob->setState(JobInterface::STATE_PENDING)->shouldBeCalled();

        $retryJob->setOriginalJob($job)->shouldBeCalled();

        $job->getSchedule()->willReturn($schedule);
        $retryJob->setSchedule($schedule)->shouldBeCalled();

        $job->getCommand()->willReturn('command');
        $retryJob->setCommand('command')->shouldBeCalled();

        $job->getArgs()->willReturn([]);
        $retryJob->setArgs([])->shouldBeCalled();

        $job->getQueue()->willReturn('default');
        $retryJob->setQueue('default')->shouldBeCalled();

        $job->getPriority()->willReturn(5);
        $retryJob->setPriority(5)->shouldBeCalled();

        $job->getMaxRuntime()->willReturn(0);
        $retryJob->setMaxRuntime(0)->shouldBeCalled();

        $this->createRetryJob($job)->shouldReturn($retryJob);
    }

    function it_creates_job_from_schedule(ScheduleInterface $schedule, FactoryInterface $factory, JobInterface $job): void
    {
        $dateTime = new \DateTime();

        $this->beConstructedWith($factory);
        $factory->createNew()->willReturn($job);

        $job->setSchedule($schedule)->shouldBeCalled();

        $schedule->getCommand()->willReturn('command');
        $job->setCommand('command')->shouldBeCalled();

        $schedule->getArgs()->willReturn([]);
        $job->setArgs([])->shouldBeCalled();

        $schedule->getNextRunDate('now')->willReturn($dateTime);
        $job->setExecuteAfter($dateTime)->shouldBeCalled();

        $this->createFromSchedule($schedule, 'now')->shouldReturn($job);
    }
}
