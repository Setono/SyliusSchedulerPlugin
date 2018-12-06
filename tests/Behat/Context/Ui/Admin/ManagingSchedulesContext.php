<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusSchedulerPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use Setono\SyliusSchedulerPlugin\Model\ScheduleInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Tests\Setono\SyliusSchedulerPlugin\Behat\Page\Admin\Schedule\CreatePageInterface;
use Tests\Setono\SyliusSchedulerPlugin\Behat\Page\Admin\Schedule\IndexPageInterface;
use Tests\Setono\SyliusSchedulerPlugin\Behat\Page\Admin\Schedule\UpdatePageInterface;
use Webmozart\Assert\Assert;

final class ManagingSchedulesContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @var CreatePageInterface
     */
    private $createPage;

    /**
     * @var UpdatePageInterface
     */
    private $updatePage;

    /**
     * @var CurrentPageResolverInterface
     */
    private $currentPageResolver;

    /**
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param IndexPageInterface $indexPage
     * @param CreatePageInterface $createPage
     * @param UpdatePageInterface $updatePage
     * @param CurrentPageResolverInterface $currentPageResolver
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        IndexPageInterface $indexPage,
        CreatePageInterface $createPage,
        UpdatePageInterface $updatePage,
        CurrentPageResolverInterface $currentPageResolver,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->indexPage = $indexPage;
        $this->createPage = $createPage;
        $this->updatePage = $updatePage;
        $this->currentPageResolver = $currentPageResolver;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @When I want to create a new schedule
     */
    public function iWantToCreateANewSchedule()
    {
        $this->createPage->open();
    }

    /**
     * @Given I want to browse schedules
     * @When I browse schedules
     */
    public function iWantToBrowseSchedules()
    {
        $this->indexPage->open();
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs($code = null)
    {
        $this->createPage->specifyCode($code);
    }

    /**
     * @When I name it :name
     * @When I do not name it
     * @When I remove its name
     */
    public function iNameIt($name = null)
    {
        $this->getCurrentPage()->nameIt($name);
    }

    /**
     * @When I specify its command as :command
     * @When I do not specify its command
     * @When I remove its command
     */
    public function iSpecifyItsCommandAs($command = null)
    {
        $this->getCurrentPage()->specifyCommand($command);
    }

    /**
     * @When I add argument :arg
     * @When I add empty argument
     */
    public function iAddArgument(?string $arg = null)
    {
        $this->getCurrentPage()->addArgument($arg);
    }

    /**
     * @When I remove its arguments
     */
    public function iRemoveItsArguments()
    {
        $this->getCurrentPage()->removeArguments();
    }

    /**
     * @When I specify its queue as :queue
     * @When I do not specify its queue
     * @When I remove its queue
     */
    public function iSpecifyItsQueueAs($queue = null)
    {
        $this->getCurrentPage()->specifyQueue($queue);
    }

    /**
     * @When I specify its priority as :priority
     * @When I do not specify its priority
     * @When I remove its priority
     */
    public function iSpecifyItsPriorityAs($priority = null)
    {
        $this->getCurrentPage()->specifyPriority(
            null == $priority ? null : (int)$priority
        );
    }

    /**
     * @When I specify its cron expression as :cronExpression
     * @When I do not specify its cron expression
     * @When I remove its cron expression
     */
    public function iSpecifyItsCronExpressionAs($cronExpression = null)
    {
        $this->getCurrentPage()->specifyCronExpression($cronExpression);
    }

    /**
     * @Then I should see the schedule :scheduleName in the list
     * @Then the :scheduleName schedule should appear in the registry
     * @Then the :scheduleName schedule should exist in the registry
     * @Then this schedule should still be named :scheduleName
     * @Then schedule :scheduleName should still exist in the registry
     */
    public function theScheduleShouldAppearInTheRegistry(string $scheduleName): void
    {
        $this->iWantToBrowseSchedules();

        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $scheduleName]));
    }

    /**
     * @Then schedule with :element :name should not be added
     */
    public function scheduleWithElementValueShouldNotBeAdded($element, $name)
    {
        $this->iWantToBrowseSchedules();

        Assert::false($this->indexPage->isSingleResourceOnPage([$element => $name]));
    }

    /**
     * @Then there should still be only one schedule with :element :value
     * @Then schedule with :element :value should still exist in the registry
     */
    public function thereShouldStillBeOnlyOneScheduleWith($element, $value)
    {
        $this->iWantToBrowseSchedules();

        Assert::true($this->indexPage->isSingleResourceOnPage([$element => $value]));
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt()
    {
        $this->createPage->create();
    }

    /**
     * @When I delete them
     */
    public function iDeleteThem(): void
    {
        $this->indexPage->bulkDelete();
    }

    /**
     * @When I check (also) the :scheduleName schedule
     */
    public function iCheckTheSchedule(string $scheduleName): void
    {
        $this->indexPage->checkResourceOnPage(['name' => $scheduleName]);
    }

    /**
     * @Then I should see a single schedule in the list
     * @Then there should be :amount schedules
     */
    public function thereShouldBeSchedule(int $amount = 1): void
    {
        Assert::same($amount, $this->indexPage->countItems());
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired($element)
    {
        $this->assertFieldValidationMessage($element, sprintf('Please enter %s.', $element));
    }

    /**
     * @Then I should be notified that cron expression is required
     */
    public function iShouldBeNotifiedThaCronExpressiontIsRequired($element = 'cronExpression')
    {
        $this->assertFieldValidationMessage($element, 'Please enter cron expression.');
    }

    /**
     * @Then I should be notified that cron expression is invalid
     */
    public function iShouldBeNotifiedThaCronExpressiontIsInvalid($element = 'cronExpression')
    {
        $this->assertFieldValidationMessage($element, 'Cron expression should be valid.');
    }

    /**
     * @Then I should be notified that schedule with this code already exists
     */
    public function iShouldBeNotifiedThatScheduleWithThisCodeAlreadyExists()
    {
        Assert::same($this->createPage->getValidationMessage('code'), 'The schedule with given code already exists.');
    }

    /**
     * @Given I want to modify a :schedule schedule
     * @Given /^I want to modify (this schedule)$/
     * @Then I should be able to modify a :schedule schedule
     */
    public function iWantToModifyASchedule(ScheduleInterface $schedule)
    {
        $this->updatePage->open(['id' => $schedule->getId()]);
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled()
    {
        Assert::true($this->updatePage->isCodeDisabled());
    }

    /**
     * @Given the :schedule schedule should have command :command
     */
    public function theScheduleShouldHaveCommand(ScheduleInterface $schedule, string $command)
    {
        $this->iWantToModifyASchedule($schedule);

        Assert::same($this->updatePage->getCommand(), $command);
    }

    /**
     * @Given the :schedule schedule should have argument :argument
     */
    public function theScheduleShouldHaveArgument(ScheduleInterface $schedule, string $argument)
    {
        $this->iWantToModifyASchedule($schedule);

        Assert::true($this->updatePage->haveArgument($argument));
    }

    /**
     * @Given the :schedule schedule should have queue :queue
     */
    public function theScheduleShouldHaveQueue(ScheduleInterface $schedule, string $queue)
    {
        $this->iWantToModifyASchedule($schedule);

        Assert::same($this->updatePage->getQueue(), $queue);
    }

    /**
     * @Given the :schedule schedule should have priority :priority
     */
    public function theScheduleShouldHavePriority(ScheduleInterface $schedule, string $priority)
    {
        $this->iWantToModifyASchedule($schedule);

        Assert::same($this->updatePage->getPriority(), (int)$priority);
    }

    /**
     * @Given the :schedule schedule should have cron expression :cronExpression
     */
    public function theScheduleShouldHaveCronExpression(ScheduleInterface $schedule, string $cronExpression)
    {
        $this->iWantToModifyASchedule($schedule);

        Assert::same($this->updatePage->getCronExpression(), $cronExpression);
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges()
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @When /^I delete a ("([^"]+)" schedule)$/
     * @When /^I try to delete a ("([^"]+)" schedule)$/
     */
    public function iDeleteSchedule(ScheduleInterface $schedule)
    {
        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['name' => $schedule->getName()]);
    }

    /**
     * @Then /^(this schedule) should no longer exist in the schedule registry$/
     */
    public function scheduleShouldNotExistInTheRegistry(ScheduleInterface $schedule)
    {
        $this->indexPage->open();

        Assert::false($this->indexPage->isSingleResourceOnPage(['code' => $schedule->getCode()]));
    }

    /**
     * @Then I should see :count schedules on the list
     */
    public function iShouldSeeSchedulesOnTheList($count)
    {
        $actualCount = $this->indexPage->countItems();

        Assert::same(
            (int) $count,
            $actualCount,
            'There should be %s schedule, but there\'s %2$s.'
        );
    }

    /**
     * @Then the first schedule on the list should have :field :value
     */
    public function theFirstScheduleOnTheListShouldHave($field, $value)
    {
        $fields = $this->indexPage->getColumnFields($field);
        $actualValue = reset($fields);

        Assert::same(
            $actualValue,
            $value,
            sprintf('Expected first schedule\'s %s to be "%s", but it is "%s".', $field, $value, $actualValue)
        );
    }

    /**
     * @Then the last schedule on the list should have :field :value
     */
    public function theLastScheduleOnTheListShouldHave($field, $value)
    {
        $fields = $this->indexPage->getColumnFields($field);
        $actualValue = end($fields);

        Assert::same(
            $actualValue,
            $value,
            sprintf('Expected last schedule\'s %s to be "%s", but it is "%s".', $field, $value, $actualValue)
        );
    }

    /**
     * @param string $element
     * @param string $expectedMessage
     */
    private function assertFieldValidationMessage($element, $expectedMessage)
    {
        Assert::same($this->getCurrentPage()->getValidationMessage($element), $expectedMessage);
    }

    /**
     * @return CreatePageInterface|UpdatePageInterface
     */
    private function getCurrentPage(): SymfonyPageInterface
    {
        return $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);
    }
}
