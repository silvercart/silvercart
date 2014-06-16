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
    
    {$CustomHtmlFormSpecialFields}

    <% if hasOnlyOneStandardAddress %>
    <div class="checkout-change-area">
        <% with AddressData %>
            <% with SilvercartInvoiceAddress %>
                <% include SilvercartAddressDetailReadOnly %>
            <% end_with %>
        <% end_with %>
        <a class="silvercart-button checkout-change-button" href="{$Controller.Link}GotoStep/{$Controller.AddressStepNumber}"><% _t('Silvercart.Change') %></a>
    </div>
    <% else %>
        <div class="subcolumns silvercart-address-equalize">
            <div class="c50l">
                <div class="subcl checkout-change-area">
                    <% with AddressData %>
                        <% with SilvercartInvoiceAddress %>
                            <% include SilvercartAddressDetailReadOnly %>
                        <% end_with %>
                    <% end_with %>
                    <a class="silvercart-button checkout-change-button" href="{$Controller.Link}GotoStep/{$Controller.AddressStepNumber}"><% _t('Silvercart.Change') %></a>
                </div>
            </div>

            <div class="c50r">
                <div class="subcr checkout-change-area">
                    <% with AddressData %>
                        <% with SilvercartShippingAddress %>
                            <% include SilvercartAddressDetailReadOnly %>
                        <% end_with %>
                    <% end_with %>
                    <a class="silvercart-button checkout-change-button" href="{$Controller.Link}GotoStep/{$Controller.AddressStepNumber}"><% _t('Silvercart.Change') %></a>
                </div>
            </div>
        </div>
    <% end_if %>
    
    <div class="subcolumns">
        <div class="c50l">
            <div class="subcl checkout-change-area">
                <div class="silvercart-highlighted-box h90">
                    <div class="silvercart-highlighted-box_content">
                        <strong><% _t('SilvercartCheckoutFormStep.CHOOSEN_SHIPPING') %>:</strong>
                        <p class="silvercart-highlighted-content">
                        <% with SilvercartShoppingCart %>
                            {$CarrierAndShippingMethodTitle} <% with ShippingMethod.ShippingFee %><% if PostPricing %>*<% end_if %><% end_with %>
                            <% if hasHandlingCostShipment %> (<span class="price highlighted">{$HandlingCostShipment.Nice}</span>)<% end_if %>
                            <% if ShippingMethod.DeliveryTime %><br/><small class="delivery-time-hint">$ShippingMethod.fieldLabel(ExpectedDelivery):<br/>{$ShippingMethod.DeliveryTime}</small><% end_if %>
                        <% end_with %>
                        </p>
                    </div>
                </div>
                <a class="silvercart-button checkout-change-button" href="{$Controller.Link}GotoStep/{$Controller.ShipmentStepNumber}"><% _t('Silvercart.Change') %></a>
            </div>
        </div>
        <div class="c50r">
            <div class="subcr checkout-change-area">
                <div class="silvercart-highlighted-box h90">
                    <div class="silvercart-highlighted-box_content">
                        <strong><% _t('SilvercartCheckoutFormStep.CHOOSEN_PAYMENT') %>:</strong>
                        <p class="silvercart-highlighted-content">
                        <% with SilvercartShoppingCart %>
                            {$payment.Name}
                            <% if hasHandlingCostPayment %> (<span class="price highlighted">{$HandlingCostPayment.Nice}</span>)<% end_if %>
                        <% end_with %>
                        </p>
                    </div>
                </div>
                <a class="silvercart-button checkout-change-button" href="{$Controller.Link}GotoStep/{$Controller.PaymentStepNumber}"><% _t('Silvercart.Change') %></a>
            </div>
        </div>
    </div>
    
    $Top.getSilvercartShoppingCartFull
    
    <% with CurrentPage %>
        <% if SilvercartConfig.ShowTaxAndDutyHint %>
        <p class="tax-and-duty-hint"><% _t('Silvercart.TaxAndDutyHint') %></p>
        <% end_if %>
    <% end_with %>
    
    <div class="actionRow">
        <div class="type-button">
            <% loop Actions %>
            $Field
            <% end_loop %>
        </div>
    </div>
</form>
