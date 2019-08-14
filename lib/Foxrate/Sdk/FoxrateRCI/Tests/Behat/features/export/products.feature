Feature: Export products
  In order to generate reviews
  As an application
  I need to export product

  Scenario: Export product
    Given I have product
    When I get request for product
    Then I return product