<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Fixture\Factory;

use Setono\SyliusSchedulerPlugin\Doctrine\ORM\JobRepository;
use Setono\SyliusSchedulerPlugin\Model\JobInterface;
use Setono\SyliusSchedulerPlugin\Model\ScheduleInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScheduleExampleFactory extends AbstractExampleFactory
{
    /**
     * @var Factory
     */
    private $scheduleFactory;

    /**
     * @var JobRepository
     */
    private $scheduleRepository;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param Factory $scheduleFactory
     * @param JobRepository $scheduleRepository
     */
    public function __construct(
        Factory $scheduleFactory,
        JobRepository $scheduleRepository
    ) {
        $this->scheduleFactory = $scheduleFactory;
        $this->scheduleRepository = $scheduleRepository;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = []): ScheduleInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var ScheduleInterface $schedule */
        $schedule = $this->scheduleFactory->createNew();
        $schedule->setCode($options['code']);
        $schedule->setName($options['name']);
        $schedule->setCommand($options['command']);

        if (isset($options['args'])) {
            $schedule->setArgs($options['args']);
        }
        if (isset($options['queue'])) {
            $schedule->setQueue($options['queue']);
        }
        if (isset($options['priority'])) {
            $schedule->setPriority((int) $options['priority']);
        }
        if (isset($options['created_at'])) {
            $schedule->setCreatedAt(new \DateTime($options['created_at']));
        }
        if (isset($options['cron_expression'])) {
            $schedule->setCronExpression($options['cron_expression']);
        }

        return $schedule;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('name', function (Options $options): string {
                return $this->faker->words(3, true);
            })
            ->setDefault('code', function (Options $options): string {
                return StringInflector::nameToCode($options['name']);
            })

            ->setDefined('command')
            ->setAllowedTypes('command', 'string')

            ->setDefined('args')
            ->setDefault('args', [])
            ->setAllowedTypes('args', 'array')

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

            ->setDefined('created_at')
            ->setAllowedTypes('created_at', ['null', 'string'])

            ->setDefined('cron_expression')
            ->setAllowedTypes('cron_expression', ['null', 'string'])
        ;
    }
}
