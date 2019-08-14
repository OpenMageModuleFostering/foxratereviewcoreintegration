Feature: Product Review Recommendations
  In order to get first impression
  As a visitor
  I need to see product review recommendations

  Scenario: Product feedback recommendation
    Given I have product with recomendations
    When I go to product details
    Then I see number of users who recommend this product

  Scenario: Product feedback total user
    Given I have product with recomendations
    When I go to product details
    Then I see total number of users who recommended and not recommended this product