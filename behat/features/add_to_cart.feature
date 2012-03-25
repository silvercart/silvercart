Feature: Add to cart
    In order to collect products for purchase
    As a website user
    I need to be able to add them to the cart

    Scenario Outline: Add product(s) to the cart from the frontpage widget group
        Given I am on "/"
        Given I should see "Cart (<initial_cart_quantity>)"
        When I fill in "SilvercartProductAddCartFormTile_customHtmlFormSubmit_1_productQuantity" with "<add_quantity>"
        And I press "SilvercartProductAddCartFormTile_customHtmlFormSubmit_1_action_customHtmlFormSubmit"
        Then I should see "Cart (<resulting_cart_quantity>)"

        Examples:
            |initial_cart_quantity|add_quantity|resulting_cart_quantity|
            |                    0|           1|                      1|
            |                    0|           4|                      4|

    Scenario Outline: Add product(s) to the cart from the product detail page
        Given I am on "/home/productgroups/payment-modules/1/paypal"
        Given I should see "Cart (<initial_cart_quantity>)"
        When I fill in "SilvercartProductAddCartFormDetail_customHtmlFormSubmit_1_productQuantity" with "<add_quantity>"
        And I press "SilvercartProductAddCartFormDetail_customHtmlFormSubmit_1_action_customHtmlFormSubmit"
        Then I should see "Cart (<resulting_cart_quantity>)"

        Examples:
            |initial_cart_quantity|add_quantity|resulting_cart_quantity|
            |                    0|           1|                      1|
            |                    0|           4|                      4|

