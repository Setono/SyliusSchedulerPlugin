@setono_sylius_scheduler @managing_schedules
Feature: Schedule validation
    In order to avoid making mistakes when managing a schedule
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Trying to add a new schedule without specifying its code
        Given I want to create a new schedule
        When I name it "Dummy schedule"
        And I do not specify its code
        And I try to add it
        Then I should be notified that code is required
        And schedule with name "Dummy schedule" should not be added

    @ui
    Scenario: Trying to add a new schedule without specifying its name
        Given I want to create a new schedule
        When I specify its code as "dummy_schedule"
        But I do not name it
        And I try to add it
        Then I should be notified that name is required
        And schedule with code "dummy_schedule" should not be added

    @ui
    Scenario: Trying to remove name from existing schedule
        Given there is a schedule "Dummy schedule" for command "dummy"
        And I want to modify this schedule
        When I remove its name
        And I try to save my changes
        Then I should be notified that name is required
        And this schedule should still be named "Dummy schedule"

    @ui
    Scenario: Trying to add a new schedule without specifying its command
        Given I want to create a new schedule
        When I specify its code as "dummy_schedule"
        But I do not name it
        And I try to add it
        Then I should be notified that name is required
        And schedule with code "dummy_schedule" should not be added

    @ui
    Scenario: Trying to remove command from existing schedule
        Given there is a schedule "Dummy schedule" for command "dummy"
        And I want to modify this schedule
        When I remove its command
        And I try to save my changes
        Then I should be notified that command is required
        And schedule with command "dummy" should still exist in the registry

    @ui
    Scenario: Trying to add a new schedule without specifying its queue
        Given I want to create a new schedule
        When I specify its code as "dummy_schedule"
        But I do not specify its queue
        And I try to add it
        Then I should be notified that queue is required
        And schedule with code "dummy_schedule" should not be added

    @ui
    Scenario: Trying to remove queue from existing schedule
        Given there is a schedule "Dummy schedule" for command "dummy"
        And this schedule has "another" queue
        And I want to modify this schedule
        When I remove its queue
        And I try to save my changes
        Then I should be notified that queue is required
        And schedule with queue "another" should still exist in the registry

    @ui
    Scenario: Trying to add a new schedule without specifying its priority
        Given I want to create a new schedule
        When I specify its code as "dummy_schedule"
        But I do not specify its priority
        And I try to add it
        Then I should be notified that priority is required
        And schedule with code "dummy_schedule" should not be added

    @ui
    Scenario: Trying to remove priority from existing schedule
        Given there is a schedule "Dummy schedule" for command "dummy"
        And this schedule has priority "7"
        And I want to modify this schedule
        When I remove its priority
        And I try to save my changes
        Then I should be notified that priority is required
        And schedule with priority "7" should still exist in the registry

    @ui
    Scenario: Trying to add a new schedule without specifying its cron expression
        Given I want to create a new schedule
        When I specify its code as "dummy_schedule"
        But I do not specify its cron expression
        And I try to add it
        Then I should be notified that cron expression is required
        And schedule with code "dummy_schedule" should not be added

    @ui
    Scenario: Trying to remove cron expression from existing schedule
        Given there is a schedule "Dummy schedule" for command "dummy"
        And this schedule has "* 1 * * *" cron expression
        And I want to modify this schedule
        When I remove its cron expression
        And I try to save my changes
        Then I should be notified that cron expression is required

    @ui
    Scenario: Trying to add schedule with invalid cron expression
        Given I want to create a new schedule
        When I specify its code as "dummy_schedule"
        And I specify its cron expression as "invalid"
        And I try to add it
        Then I should be notified that cron expression is invalid
        And schedule with code "dummy_schedule" should not be added
