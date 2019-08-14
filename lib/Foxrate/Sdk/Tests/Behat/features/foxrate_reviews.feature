Feature: Foxrate Review Page
  In order to see a Foxrate provided reviews
  As a website user
  I need to see Foxrate reviews in page

  Background: Foxrate reviews are installed and product has reviews
    Given I am on a product page
    When I click "Reviews"
    Then Product has one page of foxrate product reviews
    And Product has one review with "4" rating

  @javascript @prestashop @oxid
  Scenario: View Foxrate user reviews
    Given I am on a product page
    When I click "Reviews"
    Then I should see foxrate product reviews

  @javascript @prestashop @oxid
  Scenario: View Foxrate reviews tab
    Given I am on a product page
    When I click "Reviews"
    Then I should see foxrate product review tab content

  @javascript @prestashop @oxid
  Scenario: View Foxrate product rating box
    Given I am on a product page
    Then I should see foxrate product rating box

  @javascript @prestashop @oxid
  Scenario: View Foxrate reviews summary
    Given I am on a product page
    When I click "Reviews"
    And Product has one review with "4" rating
    Then I should see foxrate review summary with "4" big stars

  @javascript @oxid @prestashop @category
  Scenario: View Foxrate review stars in grid view default category
    Given I am on a default category page
    Then I should see foxrate product rating stars

  @javascript @oxid @category
  Scenario: View Foxrate review stars in grid view default category
    Given I select "grid" view
    Then I should see foxrate product rating stars of default product

  @javascript @oxid @category
  Scenario: View Foxrate review stars in grid view default category
    Given I select "infogrid" view
    Then I should see foxrate product rating stars

  @javascript @oxid @category
  Scenario: View Foxrate review stars in grid view default category
    Given I select "line" view
    Then I should see foxrate product rating stars