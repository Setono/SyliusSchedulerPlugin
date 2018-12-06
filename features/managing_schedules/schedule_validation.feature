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
