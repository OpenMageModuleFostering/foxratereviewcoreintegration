Feature: Import reviews
  In order to display reviews
  As an application
  I need to import reviews

  Scenario: Import product reviews
    Given I have product
    When I import reviews for this product
    Then I have reviews

  Scenario: Import product review by id
    Given I have review id
    When I import review by it's id
    Then I have review