<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusSchedulerPlugin\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Setono\SyliusSchedulerPlugin\Doctrine\ORM\ScheduleRepositoryInterface;
use Setono\SyliusSchedulerPlugin\Model\ScheduleInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Webmozart\Assert\Assert;

final class ScheduleContext implements Context
{
    /**
     * @var ScheduleRepositoryInterface
     */
    private $scheduleRepository;

    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @param ScheduleRepositoryInterface $scheduleRepository
     * @param SharedStorageInterface $sharedStorage
     */
    public function __construct(
        ScheduleRepositoryInterface $scheduleRepository,
        SharedStorageInterface $sharedStorage
    ) {
        $this->scheduleRepository = $scheduleRepository;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Transform /^schedule "([^"]+)"$/
     * @Transform /^"([^"]+)" schedule/
     * @Transform :schedule
     */
    public function getScheduleByName($name): ?ScheduleInterface
    {
        /** @var ScheduleInterface $schedule */
        $schedule = $this->scheduleRepository->findOneBy(['name' => $name]);

        Assert::notNull(
            $schedule,
            sprintf('Schedule with name "%s" does not exist', $name)
        );

        return $schedule;
    }

}
