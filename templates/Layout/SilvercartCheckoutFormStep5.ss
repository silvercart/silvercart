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
        <% with AddressData %>
            <% with SilvercartInvoiceAddress %>
                <% include SilvercartAddressDetailReadOnly %>
            <% end_with %>
        <% end_with %>
    <% else %>
        <div class="subcolumns silvercart-address-equalize">
            <div class="c50l">
                <div class="subcl">
                    <% with AddressData %>
                        <% with SilvercartInvoiceAddress %>
                            <% include SilvercartAddressDetailReadOnly %>
                        <% end_with %>
                    <% end_with %>
                </div>
            </div>

            <div class="c50r">
                <div class="subcr">
                    <% with AddressData %>
                        <% with SilvercartShippingAddress %>
                            <% include SilvercartAddressDetailReadOnly %>
                        <% end_with %>
                    <% end_with %>
                </div>
            </div>
        </div>
    <% end_if %>    
    
    $Top.getSilvercartShoppingCartFull
    
    <div class="actionRow">
        <div class="type-button">
            <% loop Actions %>
            $Field
            <% end_loop %>
        </div>
    </div>
</form>
