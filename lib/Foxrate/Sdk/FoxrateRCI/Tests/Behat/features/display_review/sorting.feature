Feature: Product feedback sorting
  In order to get impression
  As a visitor
  I need to see sorted feedback

  Scenario: Product feedback sorting by descending rating order
    Given I have product with reviews
    When I go to product details
    And I sort feedbacks by descending rating order
    Then I see feedbacks in descending rating order

  Scenario: Product feedback sorting by ascending rating order
    Given I have product with reviews
    When I go to product details
    And I sort feedbacks by ascending rating order
    Then I see feedbacks in ascending rating order

  Scenario: Product feedback sorting by descending date order
    Given I have product with reviews
    When I go to product details
    And I sort feedbacks by descending date order
    Then I see feedbacks in descending date order

  Scenario: Product feedback sorting by ascending date order
    Given I have product with reviews
    When I go to product details
    And I sort feedbacks by ascending date order
    Then I see feedbacks in ascending date order