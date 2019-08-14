Feature: Product feedback filtering
  In order to get impression
  As a visitor
  I need to see filtered feedback

  Scenario: Product feedbacks filtering by rating
    Given I have product with reviews
    When I go to product details
    And I filter reviews by one of the ratings
    Then I see only feedbacks for selected rating

  Scenario: Product feedbacks filtering by search
    Given I have product with reviews
    When I go to product details
    And I search for feedbacks using part of the comment
    Then I see only reviews which contain the search keyword