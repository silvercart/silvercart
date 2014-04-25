{$Controller.ContentStep5}
<form class="yform full" $FormAttributes >
    $CustomHtmlFormMetadata
    $CustomHtmlFormErrorMessages
    
    <div class="subcolumns">
        <div id="silvercart-checkout-privacy-check" class="c66l">
            <fieldset>
                <legend><% _t('SilvercartPage.TERMSOFSERVICE_PRIVACY') %></legend>
                $CustomHtmlFormFieldByName(HasAcceptedTermsAndConditions,SilvercartHasAcceptedTermsAndConditionsFieldCheck)
                $CustomHtmlFormFieldByName(HasAcceptedRevocationInstruction,SilvercartHasAcceptedRevocationInstructionFieldCheck)
                <% if Top.showNewsletterCheckbox %>
                    $CustomHtmlFormFieldByName(SubscribedToNewsletter,SilvercartHasAcceptedNewsletterFieldCheck)
                <% end_if %>
            </fieldset>
        </div>
        <div id="silvercart-checkout-note" class="c33r">
            <div class="subcr">
                <fieldset>
                    <legend><% _t('SilvercartPage.REMARKS') %></legend>
                    $CustomHtmlFormFieldByName(Note)
                </fieldset>
            </div>
        </div>
    </div>

    <% if hasOnlyOneStandardAddress %>
    <div class="checkout-change-area">
        <% control AddressData %>
            <% control SilvercartInvoiceAddress %>
                <% include SilvercartAddressDetailReadOnly %>
            <% end_control %>
        <% end_control %>
        <a class="silvercart-button checkout-change-button" href="{$Controller.Link}GotoStep/{$Controller.AddressStepNumber}"><% _t('Silvercart.Change') %></a>
    </div>
    <% else %>
        <div class="subcolumns silvercart-address-equalize">
            <div class="c50l">
                <div class="subcl checkout-change-area">
                    <% control AddressData %>
                        <% control SilvercartInvoiceAddress %>
                            <% include SilvercartAddressDetailReadOnly %>
                        <% end_control %>
                    <% end_control %>
                    <a class="silvercart-button checkout-change-button" href="{$Controller.Link}GotoStep/{$Controller.AddressStepNumber}"><% _t('Silvercart.Change') %></a>
                </div>
            </div>

            <div class="c50r">
                <div class="subcr checkout-change-area">
                    <% control AddressData %>
                        <% control SilvercartShippingAddress %>
                            <% include SilvercartAddressDetailReadOnly %>
                        <% end_control %>
                    <% end_control %>
                    <a class="silvercart-button checkout-change-button" href="{$Controller.Link}GotoStep/{$Controller.AddressStepNumber}"><% _t('Silvercart.Change') %></a>
                </div>
            </div>
        </div>
    <% end_if %>
    
    <div class="subcolumns">
        <div class="c50l">
            <div class="subcl checkout-change-area">
                <div class="silvercart-highlighted-box">
                    <div class="silvercart-highlighted-box_content">
                        <strong><% _t('SilvercartCheckoutFormStep.CHOOSEN_SHIPPING') %>:</strong>
                        <p class="silvercart-highlighted-content">
                        <% control SilvercartShoppingCart %>
                            {$CarrierAndShippingMethodTitle} <% control ShippingMethod.ShippingFee %><% if PostPricing %>*<% end_if %><% end_control %>
                            <% if hasHandlingCostShipment %> (<span class="price highlighted">{$HandlingCostShipment.Nice}</span>)<% end_if %>
                        <% end_control %>
                        </p>
                    </div>
                </div>
                <a class="silvercart-button checkout-change-button" href="{$Controller.Link}GotoStep/{$Controller.ShipmentStepNumber}"><% _t('Silvercart.Change') %></a>
            </div>
        </div>
        <div class="c50r">
            <div class="subcr checkout-change-area">
                <div class="silvercart-highlighted-box">
                    <div class="silvercart-highlighted-box_content">
                        <strong><% _t('SilvercartCheckoutFormStep.CHOOSEN_PAYMENT') %>:</strong>
                        <p class="silvercart-highlighted-content">
                        <% control SilvercartShoppingCart %>
                            {$payment.Name}
                            <% if hasHandlingCostPayment %> (<span class="price highlighted">{$HandlingCostPayment.Nice}</span>)<% end_if %>
                        <% end_control %>
                        </p>
                    </div>
                </div>
                <a class="silvercart-button checkout-change-button" href="{$Controller.Link}GotoStep/{$Controller.PaymentStepNumber}"><% _t('Silvercart.Change') %></a>
            </div>
        </div>
    </div>
    
    $Top.getSilvercartShoppingCartFull
    
    <% control CurrentPage %>
        <% if SilvercartConfig.ShowTaxAndDutyHint %>
        <p class="tax-and-duty-hint"><% _t('Silvercart.TaxAndDutyHint') %></p>
        <% end_if %>
    <% end_control %>
    
    <div class="actionRow">
        <div class="type-button">
            <% control Actions %>
            $Field
            <% end_control %>
        </div>
    </div>
</form>
