@setono_sylius_scheduler @managing_schedules
Feature: Adding a new schedule
    In order to schedule commands execution
    As an Administrator
    I want to be able to add new schedules

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Adding a new schedule
        Given I want to create a new schedule
        When I specify its code as "DUMMY_SCHEDULE"
        And I name it "Dummy schedule"
        And I specify its command as "dummy"
        And I add it
        And I should be notified that it has been successfully created
        Then the "Dummy schedule" schedule should appear in the registry
