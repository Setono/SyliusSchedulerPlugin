<?php

namespace spec\Setono\SyliusSchedulerPlugin\Model;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Setono\SyliusSchedulerPlugin\Model\JobInterface;
use Setono\SyliusSchedulerPlugin\Model\Schedule;
use Setono\SyliusSchedulerPlugin\Model\ScheduleInterface;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

class ScheduleSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Schedule::class);
    }

    function it_implements_interface(): void
    {
        $this->shouldImplement(ScheduleInterface::class);
    }

    function it_implements_resource_interface(): void
    {
        $this->shouldImplement(ResourceInterface::class);
    }

    function it_implements_code_aware_interface(): void
    {
        $this->shouldImplement(CodeAwareInterface::class);
    }

    function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_code_by_default(): void
    {
        $this->getCode()->shouldReturn(null);
    }

    function its_code_is_mutable(): void
    {
        $this->setCode('some_job');
        $this->getCode()->shouldReturn('some_job');
    }

    function it_has_no_name_by_default(): void
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable(): void
    {
        $this->setName('Some job');
        $this->getName()->shouldReturn('Some job');
    }

    function it_has_no_command_by_default(): void
    {
        $this->getCommand()->shouldReturn(null);
    }

    function its_command_is_mutable(): void
    {
        $this->setCommand('echo');
        $this->getCommand()->shouldReturn('echo');
    }

    function it_has_empty_args_list_by_default(): void
    {
        $this->getArgs()->shouldReturn([]);
    }

    function its_args_is_mutable(): void
    {
        $this->setArgs(['hi']);
        $this->getArgs()->shouldReturn(['hi']);
    }

    function it_has_queue_by_default(): void
    {
        $this->getQueue()->shouldReturn(JobInterface::DEFAULT_QUEUE);
    }

    function its_queue_is_mutable(): void
    {
        $this->setQueue('queue');
        $this->getQueue()->shouldReturn('queue');
    }

    function it_has_priority_by_default(): void
    {
        $this->getPriority()->shouldReturn(JobInterface::PRIORITY_DEFAULT);
    }

    function its_priority_is_mutable(): void
    {
        $this->setPriority(1);
        $this->getPriority()->shouldReturn(1);
    }

    function it_has_cron_expression_by_default(): void
    {
        $this->getCronExpression()->shouldReturn(ScheduleInterface::DEFAULT_CRON_EXPRESSION);
    }

    function its_cron_expression_is_mutable(): void
    {
        $this->setCode('* */30 * * *');
        $this->getCode()->shouldReturn('* */30 * * *');
    }

    function it_initializes_jobs_collection_by_default(): void
    {
        $this->getJobs()->shouldHaveType(Collection::class);
    }

    function it_has_no_jobs_by_default(): void
    {
        $this->hasJobs()->shouldReturn(false);
    }

    function it_adds_job(JobInterface $job): void
    {
        $this->addJob($job);
        $this->hasJobs()->shouldReturn(true);
        $this->hasJob($job)->shouldReturn(true);
    }

    function it_removes_job(JobInterface $job): void
    {
        $this->addJob($job);
        $this->hasJob($job)->shouldReturn(true);
        $this->removeJob($job);
        $this->hasJob($job)->shouldReturn(false);
    }

    function it_has_created_at_initialized_by_default(): void
    {
        $this->getCreatedAt()->shouldNotReturn(null);
        $this->getCreatedAt()->shouldBeAnInstanceOf(\DateTime::class);
    }

    function its_created_at_is_mutable(\DateTime $now): void
    {
        $this->setCreatedAt($now);
        $this->getCreatedAt()->shouldReturn($now);
    }

    function its_latest_job_returns_null_by_default(): void
    {
        $this->getLatestJob()->shouldBeNull();
    }

    function its_latest_job_returns_last_added_job(JobInterface $job1, JobInterface $job2): void
    {
        $this->addJob($job1);
        $this->addJob($job2);
        $this->getLatestJob()->shouldNotBeEqualTo($job1);
        $this->getLatestJob()->shouldBeEqualTo($job2);
    }

    public function its_next_run_date_implements_date_time(): void
    {
        $this->getNextRunDate()->shouldBeAnInstanceOf(\DateTime::class);
    }

    public function next_job_should_be_created_by_default(): void
    {
        $this->isNextJobShouldBeCreated()->shouldReturn(true);
    }

}
