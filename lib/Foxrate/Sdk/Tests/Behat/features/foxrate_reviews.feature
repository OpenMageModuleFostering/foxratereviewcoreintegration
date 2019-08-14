Feature: Foxrate Review Page
  In order to see a Foxrate provided reviews
  As a website user
  I need to see Foxrate reviews in page

  @javascript @prestashop
  Scenario: View Foxrate reviews tab
    Given I am on a product page
    When I click "Reviews"
    Then I should see foxrate product reviews

  @javascript @prestashop
  Scenario: View Foxrate reviews
    Given I am on a product page
    When I click "Reviews"
    Then I should not see foxrate product rating box

  @javascript @prestashop @this
  Scenario: View Foxrate reviews summary
    Given I am on a product page
    When I click "Reviews"
    And Product has one review with "4" rating
    Then I should see foxrate review summary with "4" big stars