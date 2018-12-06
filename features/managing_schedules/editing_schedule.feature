@setono_sylius_scheduler @managing_schedules
Feature: Editing schedule
    In order to change schedule details
    As an Administrator
    I want to be able to edit a schedule

    Background:
        Given there is a schedule "Dummy schedule" for command "dummy"
        And I am logged in as an administrator

    @ui
    Scenario: Seeing disabled code field when editing schedule
        When I want to modify a "Dummy schedule" schedule
        Then the code field should be disabled

    @ui
    Scenario: Updating existing schedule name
        Given I want to modify a "Dummy schedule" schedule
        When I name it "Updated schedule"
        And I save my changes
        Then I should be notified that it has been successfully edited

    @ui
    Scenario: Updating existing schedule command
        Given I want to modify a "Dummy schedule" schedule
        When I specify its command as "another"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "Dummy schedule" schedule should have command "another"

    @ui @javascript
    Scenario: Updating existing schedule arguments
        Given I want to modify a "Dummy schedule" schedule
        When I add argument "hey"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "Dummy schedule" schedule should have argument "hey"

    @ui
    Scenario: Updating existing schedule queue
        Given I want to modify a "Dummy schedule" schedule
        When I specify its queue as "another"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "Dummy schedule" schedule should have queue "another"

    @ui
    Scenario: Updating existing schedule priority
        Given I want to modify a "Dummy schedule" schedule
        When I specify its priority as 7
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "Dummy schedule" schedule should have priority 7

    @ui
    Scenario: Updating existing schedule cron expression
        Given I want to modify a "Dummy schedule" schedule
        When I specify its cron expression as "*/2 */3 * * *"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "Dummy schedule" schedule should have cron expression "*/2 */3 * * *"
