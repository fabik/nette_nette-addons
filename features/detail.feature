# pepa: nova
Feature: ls
  In order to work with the package
  As a random user
  I need to be able to see details of a given package

  Scenario: Details of a package
    Given There are packages:
      | id | name      | url                |
      | 1  | WebLoader | JanMarek/WebLoader |

    Then I should see "WebLoader" in selector "h1"
    And I should see "JanMarek/WebLoader" in selector "h4.vendor"
