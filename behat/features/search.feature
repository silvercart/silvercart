Feature: Search
    In order to get search results
    As a website user
    I need to be able to search for a word

    Scenario: Searching for an existing product
        Given I am on "/"
         When I fill in "quickSearchQuery" with "PayPal"
          And I press "SilvercartQuickSearchForm_customHtmlFormSubmit_1_action_customHtmlFormSubmit"
         Then I should see "Search results for query ”PayPal” (1 results):"

    Scenario: Searching for a non-existing product
        Given I am on "/"
         When I fill in "quickSearchQuery" with "NonExistantProductName"
          And I press "SilvercartQuickSearchForm_customHtmlFormSubmit_1_action_customHtmlFormSubmit"
         Then I should see "Search results for query ”NonExistantProductName” (0 results):"

