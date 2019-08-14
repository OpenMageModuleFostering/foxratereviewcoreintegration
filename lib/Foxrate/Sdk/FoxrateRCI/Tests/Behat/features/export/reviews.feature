Feature: Migrate reviews
  In order to replace initial review system
  As an application
  I need to migrate reviews

  Scenario: Return review
    Given I have review
    When I get request for review
    Then I return review

  Scenario: Migrate review
    Given I have reviews in shop system
    When I perform migration
    Then I have reviews in review system