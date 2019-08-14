Feature: Product Rich Snippets
  In order to provide product impression to visitors
  As an application
  I need to provide rich snippets

  Scenario: Product Name Rich Snippet
    Given I have product
    When I am on product details page
    Then I see product name rich snippet

  Scenario: Product Review Rating Rich Snippet
    Given I have product with reviews
    When I am on product details page
    Then I see product aggregating rating rich snippet

  Scenario: Product Review Count Rich Snippet
    Given I have product with reviews
    When I am on product details page
    Then I see product review count rich snippet

  Scenario: Product Review Author Rich Snippet
    Given I have product with reviews
    When I am on product details page
    Then I see product review author rich snippet

  Scenario: Product Review Name Rich Snippet
    Given I have product with reviews
    When I am on product details page
    Then I see product review name rich snippet

  Scenario: Product Review Description Rich Snippet
    Given I have product with reviews
    When I am on product details page
    Then I see product review description rich snippet

  Scenario: Product Review Publish Date Rich Snippet
    Given I have product with reviews
    When I am on product details page
    Then I see product review publish date rich snippet

  Scenario: Product Review Rating Rich Snippet
    Given I have product with reviews
    When I am on product details page
    Then I see product rating minimum value rich snippet
    And I see product review rating rich snippet
    And I see product rating maximum value rich snippet
