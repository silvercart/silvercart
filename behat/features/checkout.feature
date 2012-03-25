Feature: Perform the checkout process
    In order to buy a product
    As an unregistered customer
    I need to be able to finish the checkout process

    @javascript
    Scenario: Add 1 product to the cart from the frontpage widget group
        Given I am on "/"
         When I press "SilvercartProductAddCartFormTile_customHtmlFormSubmit_1_action_customHtmlFormSubmit"
         Then I should see "Cart (1)"

    @javascript
    Scenario: Go to the checkout page
        Given I should see "Cart (1)"
         When I follow "silvercart-checkout-link"
         Then I should see "Registration"
          And I should see "Addresses"
          And I should see "Shipment"
          And I should see "Payment"
          And I should see "Overview"

    @javascript
    Scenario: Choose the "don't register" option and continue
        Given I should see "No, I don't want to register."
         When I fill in "SilvercartCheckoutFormStep1NewCustomerForm_customHtmlFormSubmit_1_AnonymousOptions_2" with "2"
         When I press "SilvercartCheckoutFormStep1NewCustomerForm_customHtmlFormSubmit_1_action_customHtmlFormSubmit"
         Then I should see "Email address"
          And I should see "Invoice address"
          And I should see "Shipping address"

    @javascript
    Scenario: Test javascript validation in registration form
        Given I should see "Email address"
        Given I should see "Invoice address"
        Given I should see "Shipping address"
         When I press "SilvercartCheckoutFormStep2Anonymous_customHtmlFormSubmit_1_action_customHtmlFormSubmit"
         Then I should see "Please enter a valid email address."
          And I should see "This field may not be empty."

    @javascript
    Scenario: Fill out the registration form and continue in the checkout process
        Given I fill in "SilvercartCheckoutFormStep2Anonymous_customHtmlFormSubmit_1_Email" with "skoehler@pixeltricks.de"
        Given I select "Herr" from "Invoice_Salutation"
        Given I fill in "Invoice_FirstName" with "John"
        Given I fill in "Invoice_Surname" with "Doe"
        Given I fill in "Invoice_Street" with "Merkurstr."
        Given I fill in "Invoice_StreetNumber" with "9"
        Given I fill in "Invoice_Postcode" with "67663"
        Given I fill in "Invoice_City" with "Kaiserslautern"
        Given I fill in "Invoice_PhoneAreaCode" with "0631"
        Given I fill in "Invoice_Phone" with "3547720"
        Given I select "234" from "Invoice_Country"
         When I press "SilvercartCheckoutFormStep2Anonymous_customHtmlFormSubmit_1_action_customHtmlFormSubmit"
         Then I should see "Shipping method"
          And I should see "Registration"
          And I should see "Addresses"
          And I should see "Shipment"
          And I should see "Payment"
          And I should see "Overview"

    @javascript
    Scenario: Try to skip choosing a shipping method
        Given I should see "Shipping method"
         When I press "SilvercartCheckoutFormStep3_customHtmlFormSubmit_1_action_customHtmlFormSubmit"
         Then I should see "This field may not be empty."

    @javascript
    Scenario: Choose a shipping method and continue in the checkout process
        Given I select "1" from "SilvercartCheckoutFormStep3_customHtmlFormSubmit_1_ShippingMethod"
         When I press "SilvercartCheckoutFormStep3_customHtmlFormSubmit_1_action_customHtmlFormSubmit"
         Then I should see "Payment method"
          And I should see "Registration"
          And I should see "Addresses"
          And I should see "Shipment"
          And I should see "Payment"
          And I should see "Overview"

    @javascript
    Scenario: Choose a prepayment payment method and continue in the checkout process
        Given I should see "Payment method"
         When I press "SilvercartCheckoutFormStep4DefaultPayment_customHtmlFormSubmit_2_action_customHtmlFormSubmit"
         Then I should see "Terms of service and privacy statement"
          And I should see "DHL-Package"
          And I should see "Prepayment"
          And I should see "Value of goods"
          And I should see "€9.99"
          And I should see "€3.90"
          And I should see "€13.89"
          And I should see "John"
          And I should see "Doe"
          And I should see "Merkurstr."
          And I should see "9"
          And I should see "67663"
          And I should see "Kaiserslautern"
          And I should see "0631"
          And I should see "3547720"
          And I should see "United States"

    @javascript
    Scenario: Try to send the order without accepting terms of service and reading revocation instructions
        Given I should see "Terms of service and privacy statement"
         When I press "SilvercartCheckoutFormStep5_customHtmlFormSubmit_1_action_customHtmlFormSubmit"
         Then I should see "This field may not be empty."

    @javascript
    Scenario: Send the order
        Given I should see "Terms of service and privacy statement"
        Given I check "SilvercartCheckoutFormStep5_customHtmlFormSubmit_1_HasAcceptedTermsAndConditions"
        Given I check "SilvercartCheckoutFormStep5_customHtmlFormSubmit_1_HasAcceptedRevocationInstruction"
         When I press "SilvercartCheckoutFormStep5_customHtmlFormSubmit_1_action_customHtmlFormSubmit"
         Then I should see "Your order is completed"
