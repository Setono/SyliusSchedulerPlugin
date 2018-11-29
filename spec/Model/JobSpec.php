<?php

namespace spec\Setono\SyliusSchedulerPlugin\Model;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Setono\SyliusSchedulerPlugin\Model\Job;
use Setono\SyliusSchedulerPlugin\Model\JobInterface;
use Setono\SyliusSchedulerPlugin\Model\ScheduleInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

class JobSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Job::class);
    }

    function it_implements_interface(): void
    {
        $this->shouldImplement(JobInterface::class);
    }

    function it_implements_resource_interface(): void
    {
        $this->shouldImplement(ResourceInterface::class);
    }

    function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_state_initialized(): void
    {
        $this->getState()->shouldReturn(JobInterface::STATE_NEW);
    }

    function it_has_no_schedule_by_default(): void
    {
        $this->getSchedule()->shouldReturn(null);
    }

    function its_schedule_is_mutable(ScheduleInterface $schedule): void
    {
        $this->setSchedule($schedule);
        $this->getSchedule()->shouldReturn($schedule);
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

    function it_has_no_started_at_by_default(): void
    {
        $this->getStartedAt()->shouldReturn(null);
    }

    function its_started_at_is_mutable(\DateTime $now): void
    {
        $this->setStartedAt($now);
        $this->getStartedAt()->shouldReturn($now);
    }

    function it_has_no_closed_at_by_default(): void
    {
        $this->getClosedAt()->shouldReturn(null);
    }

    function its_closed_at_is_mutable(\DateTime $now): void
    {
        $this->setClosedAt($now);
        $this->getClosedAt()->shouldReturn($now);
    }

    function it_has_execute_after_initialized_by_default(): void
    {
        $this->getExecuteAfter()->shouldNotReturn(null);
        $this->getExecuteAfter()->shouldBeAnInstanceOf(\DateTime::class);
    }

    function its_execute_after_is_mutable(\DateTime $now): void
    {
        $this->setExecuteAfter($now);
        $this->getExecuteAfter()->shouldReturn($now);
    }

    function it_has_worker_name_by_default(): void
    {
        $this->getWorkerName()->shouldReturn(null);
    }

    function its_worker_name_is_mutable(): void
    {
        $this->setWorkerName('server.local');
        $this->getWorkerName()->shouldReturn('server.local');
    }

    function it_has_no_output_by_default(): void
    {
        $this->getOutput()->shouldReturn(null);
    }

    function its_output_is_mutable(): void
    {
        $this->setOutput('Output');
        $this->getOutput()->shouldReturn('Output');
    }

    function it_has_no_error_output_by_default(): void
    {
        $this->getErrorOutput()->shouldReturn(null);
    }

    function its_error_output_is_mutable(): void
    {
        $this->setErrorOutput('Error output');
        $this->getErrorOutput()->shouldReturn('Error output');
    }

    function it_has_no_exit_code_by_default(): void
    {
        $this->getExitCode()->shouldReturn(null);
    }

    function its_exit_code_is_mutable(): void
    {
        $this->setExitCode(0);
        $this->getExitCode()->shouldReturn(0);
    }

    function it_has_no_max_retries_by_default(): void
    {
        $this->getExitCode()->shouldReturn(null);
    }

    function its_max_retries_is_mutable(): void
    {
        $this->setExitCode(0);
        $this->getExitCode()->shouldReturn(0);
    }

    function it_return_itself_as_original_job_by_default(): void
    {
        $this->getOriginalJob()->shouldReturn($this);
    }

    function its_original_job_is_mutable(JobInterface $originalJob): void
    {
        $this->setOriginalJob($originalJob);
        $this->getOriginalJob()->shouldReturn($originalJob);
    }

    function it_initializes_retry_jobs_collection_by_default(): void
    {
        $this->getRetryJobs()->shouldHaveType(Collection::class);
    }

    function it_has_no_retry_jobs_by_default(): void
    {
        $this->hasRetryJobs()->shouldReturn(false);
    }

    function it_adds_retry_job(JobInterface $job): void
    {
        $job->getOriginalJob()->willReturn($job);
        $job->setOriginalJob($this)->shouldBeCalled();

        $this->addRetryJob($job);

        $this->hasRetryJobs()->shouldReturn(true);
        $this->hasRetryJob($job)->shouldReturn(true);
    }

    function it_removes_retry_job(JobInterface $job): void
    {
        $job->getOriginalJob()->willReturn($job);
        $job->setOriginalJob($this)->shouldBeCalled();

        $this->addRetryJob($job);
        $this->hasRetryJob($job)->shouldReturn(true);

        $job->setOriginalJob(null)->shouldBeCalled();
        $this->removeRetryJob($job);
        $this->hasRetryJob($job)->shouldReturn(false);
    }

}
