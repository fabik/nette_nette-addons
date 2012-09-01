Feature: Addon detail
  In order to work with the package
  As a random user
  I need to be able to see details of a given package

  Scenario: Details of a package
    Given Clean database
    And There are packages:
      | id | name      | url                |
      | 1  | WebLoader | JanMarek/WebLoader |

    When I am on "/detail/1"
    Then I should see "WebLoader" in selector "h1"
    And I should see "JanMarek/WebLoader" in selector "h4.vendor"

    And I should see 2 "div.box-tags a.label.label-info" elements
    And I should see 4 "div.box-tags a.label" elements
