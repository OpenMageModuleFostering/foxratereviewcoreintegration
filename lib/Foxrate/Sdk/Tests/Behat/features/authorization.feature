Feature: Foxrate Authorization
  In order to display a Foxrate provided reviews
  As a site owner
  I need to be authorised with Foxrate

  @javascript @prestashop @oxid @this
  Scenario: Test connection
    Given I request "connection_test" page
    Then The response is Json
    And I should display my Foxrate username


  @javascript @prestashop @oxid @this
  Scenario: Test connection
    Given I request "check" page
    Then The response is Json
    And I should "not" display error
    And I should get upload id