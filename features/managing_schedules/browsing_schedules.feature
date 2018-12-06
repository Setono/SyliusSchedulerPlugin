@setono_sylius_scheduler @managing_schedules
Feature: Browsing schedules
    In order to have ability to manage schedules
    As an Administrator
    I want to browse existing schedules

    Background:
        Given there is a schedule "Dummy schedule" for command "dummy"
        And I am logged in as an administrator

    @ui
    Scenario: Browsing schedules
        Given I want to browse schedules
        Then I should see a single schedule in the list
        And the "Dummy schedule" schedule should exist in the registry
