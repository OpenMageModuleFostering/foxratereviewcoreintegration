Feature: Product Review Counts
  In order to get first impression
  As a visitor
  I need to see product review counts

  Scenario: Product feedback counts for each rating
    Given I have product with reviews
    When I go to product profile
    Then I see rating counts for all ratings

  Scenario: Product feedback count bar for each rating
    Given I have product with reviews
    When I go to product profile
    Then I see rating count bar for all ratings