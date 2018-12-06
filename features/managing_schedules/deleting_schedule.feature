@setono_sylius_scheduler @managing_schedules
Feature: Deleting a schedule
    In order to remove test, obsolete or incorrect schedules
    As an Administrator
    I want to be able to delete a schedule from the registry

    Background:
        Given there is a schedule "Dummy schedule" for command "dummy"
        And I am logged in as an administrator

    @domain @ui
    Scenario: Deleted schedule should disappear from the registry
        When I delete a "Dummy schedule" schedule
        Then I should be notified that it has been successfully deleted
        And this schedule should no longer exist in the schedule registry
