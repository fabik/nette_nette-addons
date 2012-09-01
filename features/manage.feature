Feature: Managing an addon
  I need to be able to add new addons

  Scenario: Not logged in cannot add an addon
    Given clean database
    When I am not logged in
    Then I should not see "Create New Addon"

    When I go to "/manage/add"
    Then I should be on "/sign/in"


  Scenario: Adding new addon
    Given clean database
    When I log in as panda:heslo
    Then I should see "New Addon"

    When I follow "Create New Addon"
    Then I should be on "/manage/add"

    When I fill in "url" with "https://github.com/janmarek/WebLoader"
    And I press "Import"

    Then I should see the following fields:
      | Name              | WebLoader |
      | Short description | Tool for loading or deploying CSS and JS files into web pages |
      | Description       | WebLoader .+ |
      | Demo URL          |   |

    When I press "Next"
    # further from here it's not done
    Then I should see "Successfully added"
