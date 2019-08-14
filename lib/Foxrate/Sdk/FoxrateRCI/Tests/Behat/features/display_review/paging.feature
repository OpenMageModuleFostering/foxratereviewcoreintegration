Feature: Product feedback paging
  In order to get impression
  As a visitor
  I need to see a lot of feedbacks through pages

  Scenario: Product feedbacks first page
    Given I have products with a lot of feedbacks
    When I go to product details
    Then I see first page of reviews

  Scenario: Product feedbacks next page
    Given I have products with a lot of feedbacks
    When I go to product details
    And I go to next page
    Then I see second page of reviews

  Scenario: Product feedbacks previous page
    Given I have products with a lot of feedbacks
    When I go to product details second page
    And I go to previous page
    Then I see first page of reviews