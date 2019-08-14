Feature: Product Rich Snippets
  In order to provide product impression to visitors
  As an application
  I need to provide rich snippets

  Background: Foxrate reviews are installed and product has reviews
    Given Product has one page of foxrate product reviews
    And Product has one review with "4" rating
    When I am on a product page
    And I click "Reviews"
    Then I should see foxrate product reviews
    And I should see foxrate product review summary
    And I should see foxrate product rating box

  @bamboo
  Scenario: Product Scope Rich Snippet Annotation
    Given I have default product
    When I am on product details page
    Then I see product scope with annotation

  @bamboo
  Scenario: Product Name Rich Snippet
    Given I have product
    When I am on product details page
    Then I see product name rich snippet

  @bamboo
  Scenario: Product Review Rating Rich Snippet
    Given I have product with reviews
    When I am on product details page
    Then I see product aggregating rating rich snippet

  @bamboo
  Scenario: Product Review Count Rich Snippet
    Given I have product with reviews
    When I am on product details page
    Then I see product review count rich snippet

  @bamboo
  Scenario: Product Review Author Rich Snippet
    Given I have product with reviews
    When I am on product details page
    Then I see product review author rich snippet

  @bamboo
  Scenario: Product Review Name Rich Snippet
    Given I have product with reviews
    When I am on product details page
    Then I see product review name rich snippet

  @bamboo
  Scenario: Product Review Description Rich Snippet
    Given I have product with reviews
    When I am on product details page
    Then I see product review description rich snippet

  @bamboo
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
