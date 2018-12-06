@setono_sylius_scheduler @managing_schedules
Feature: Sorting listed schedules by priority
    In order to change the order by which schedules are used
    As an Administrator
    I want to sort schedules by their priority

    Background:
        Given there is a schedule "Highest" for command "highest" with priority 2
        And there is a schedule "Middle" for command "middle" with priority 2
        And there is a schedule "Lower" for command "lower" with priority 1
        And I am logged in as an administrator

    @ui
    Scenario: Schedules are sorted by priority in descending order and id in ascending order by default
        When I want to browse schedules
        Then I should see 3 schedules on the list
        And the first schedule on the list should have name "Highest"
        And the last schedule on the list should have name "Lower"

    @ui
    Scenario: Schedule's default priority is 0 which puts it at the bottom of the list
        Given there is a schedule "Default" for command "default"
        When I want to browse schedules
        Then I should see 4 schedules on the list
        And the last schedule on the list should have name "Default"
