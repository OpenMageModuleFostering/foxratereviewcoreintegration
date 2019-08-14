Feature: Reviews
  In order to get impression
  As a visitor
  I need to see reviews

  Scenario: Display product review
    Given I have product review
    When I go to product details
    Then I see product review

  Scenario: Display product review comment
    Given I have product review with review comment
    When I go to product details
    Then I see product review with comment

  Scenario: Display product review cons
    Given I have product review with review cons
    When I go to product details
    Then I see product review with cons

  Scenario: Display product review pros
    Given I have product review with review pros
    When I go to product details
    Then I see product review with pros

  Scenario: Display product review conclusion
    Given I have product review with review conclusion
    When I go to product details
    Then I see product review with conclusion