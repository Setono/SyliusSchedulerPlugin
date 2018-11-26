<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Fixture\Factory;

use Setono\SyliusSchedulerPlugin\Doctrine\ORM\JobRepository;
use Setono\SyliusSchedulerPlugin\Doctrine\ORM\ScheduleRepository;
use Setono\SyliusSchedulerPlugin\Factory\JobFactory;
use Setono\SyliusSchedulerPlugin\Model\JobInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobExampleFactory extends AbstractExampleFactory
{
    /**
     * @var ScheduleRepository
     */
    private $scheduleRepository;

    /**
     * @var JobFactory
     */
    private $jobFactory;

    /**
     * @var JobRepository
     */
    private $jobRepository;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param ScheduleRepository $scheduleRepository
     * @param JobFactory $jobFactory
     * @param JobRepository $jobRepository
     */
    public function __construct(
        ScheduleRepository $scheduleRepository,
        JobFactory $jobFactory,
        JobRepository $jobRepository
    ) {
        $this->scheduleRepository = $scheduleRepository;
        $this->jobFactory = $jobFactory;
        $this->jobRepository = $jobRepository;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = []): JobInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var JobInterface $job */
        $job = $this->jobFactory->createNew();

        if (isset($options['schedule'])) {
            $job->setSchedule($options['schedule']);
        }
        $job->setCommand($options['command']);

        if (isset($options['args'])) {
            $job->setArgs($options['args']);
        }
        if (isset($options['state'])) {
            $job->setState($options['state']);
        }
        if (isset($options['queue'])) {
            $job->setQueue($options['queue']);
        }
        if (isset($options['priority'])) {
            $job->setPriority((int) $options['priority']);
        }
//        if (isset($options['created_at'])) {
//            $job->setCreatedAt(new \DateTime($options['created_at']));
//        }
//        if (isset($options['started_at'])) {
//            $job->setStartedAt(new \DateTime($options['started_at']));
//        }
//        if (isset($options['checked_at'])) {
//            $job->setCheckedAt(new \DateTime($options['checked_at']));
//        }
//        if (isset($options['closed_at'])) {
//            $job->setClosedAt(new \DateTime($options['closed_at']));
//        }
        if (isset($options['execute_after'])) {
            $job->setExecuteAfter(new \DateTime($options['execute_after']));
        }

//        /** @var JobInterface $dependencyJob */
//        foreach ($options['dependencies'] as $dependencyJob) {
//            $job->addDependency($dependencyJob);
//        }

        if (isset($options['worker_name'])) {
            $job->setWorkerName($options['worker_name']);
        }
        if (isset($options['output'])) {
            $job->setOutput($options['output']);
        }
        if (isset($options['error_output'])) {
            $job->setErrorOutput($options['error_output']);
        }
        if (isset($options['exit_code'])) {
            $job->setExitCode($options['exit_code']);
        }
        if (isset($options['max_runtime'])) {
            $job->setMaxRuntime($options['max_runtime']);
        }
        if (isset($options['max_retries'])) {
            $job->setMaxRetries($options['max_retries']);
        }

//        if (isset($options['original_job'])) {
//            $job->setOriginalJob($options['original_job']);
//        }

        if (isset($options['retry_jobs']) && is_int($options['retry_jobs'])) {
            /** @var JobInterface $retryJob */
            for ($i = 0; $i < $options['retry_jobs']; $i++) {
                $retryJob = $this->jobFactory->createRetryJob($job);
                $job->addRetryJob($retryJob);
            }
        }

        return $job;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefined('schedule')
            ->setNormalizer('schedule', LazyOption::findOneBy($this->scheduleRepository, 'code'))
            ->setAllowedTypes('schedule', ['null', 'string'])

            ->setDefined('command')
            ->setAllowedTypes('command', 'string')

            ->setDefined('args')
            ->setDefault('args', [])
            ->setAllowedTypes('args', 'array')

            ->setDefined('state')
            ->setAllowedTypes('state', ['null', 'string'])

            ->setDefined('queue')
            ->setAllowedTypes('queue', ['null', 'string'])

            ->setDefault('priority', function (Options $options): int {
                return $this->faker->randomElement([
                    JobInterface::PRIORITY_LOW,
                    JobInterface::PRIORITY_DEFAULT,
                    JobInterface::PRIORITY_HIGH,
                ]);
            })
            ->setAllowedTypes('priority', ['int', 'null'])

//            ->setDefined('created_at')
//            ->setAllowedTypes('created_at', ['null', 'string'])
//
//            ->setDefined('started_at')
//            ->setAllowedTypes('started_at', ['null', 'string'])
//
//            ->setDefined('checked_at')
//            ->setAllowedTypes('checked_at', ['null', 'string'])
//
//            ->setDefined('closed_at')
//            ->setAllowedTypes('closed_at', ['null', 'string'])

            ->setDefined('execute_after')
            ->setAllowedTypes('execute_after', ['null', 'string'])

//            // @todo Decide how to identify
//            ->setNormalizer('dependencies', LazyOption::findBy($this->jobRepository, '...'))
//            ->setAllowedTypes('dependencies', 'array')

            ->setDefined('worker_name')
            ->setAllowedTypes('worker_name', ['null', 'string'])

            ->setDefined('output')
            ->setAllowedTypes('output', ['null', 'string'])

            ->setDefined('error_output')
            ->setAllowedTypes('error_output', ['null', 'string'])

            ->setDefined('exit_code')
            ->setAllowedTypes('exit_code', ['null', 'int'])

            ->setDefined('max_runtime')
            ->setAllowedTypes('max_runtime', ['null', 'int'])

            ->setDefined('max_retries')
            ->setAllowedTypes('max_retries', ['null', 'int'])

//            // @todo Decide how to identify
//            ->setNormalizer('retry_jobs', LazyOption::findOneBy($this->jobRepository, '...'))
//            ->setAllowedTypes('original_job', ['null', 'object'])

            ->setDefined('retry_jobs')
            ->setNormalizer('retry_jobs', function($options){
                return $this->faker->numberBetween(0, 4);
            })
            ->setAllowedTypes('retry_jobs', 'int')
        ;
    }
}
