Feature: Export customers
  In order to generate reviews
  As an application
  I need to export customer

  Scenario: Export customer
    Given I have customer
    When I get request for customer
    Then I return customer

