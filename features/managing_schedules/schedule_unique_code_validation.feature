@setono_sylius_scheduler @managing_schedules
Feature: Schedule unique code validation
    In order to uniquely identify schedules
    As an Administrator
    I want to be prevented from adding two schedules with the same code

    Background:
        Given there is a schedule "Dummy schedule" for command "dummy" identified by "DUMMY_SCHEDULE" code
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add schedule with taken code
        Given I want to create a new schedule
        When I specify its code as "DUMMY_SCHEDULE"
        And I name it "Another dummy schedule"
        And I specify its command as "dummy"
        And I try to add it
        Then I should be notified that schedule with this code already exists
        And there should still be only one schedule with code "DUMMY_SCHEDULE"
