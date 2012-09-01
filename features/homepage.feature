Feature: Homepage
  In order to ???

  Scenario: List of Addons on homepage
    Given Clean database
    When I am on homepage
    Then I should see "Addons" in selector "title"
