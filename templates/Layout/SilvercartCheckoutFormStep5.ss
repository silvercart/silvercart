<form class="yform full" $FormAttributes >
    $CustomHtmlFormMetadata
    $CustomHtmlFormErrorMessages
    
    <div class="subcolumns">
        <div id="silvercart-checkout-privacy-check" class="c66l">
            <fieldset  style="height: 150px">
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
                <fieldset   style="height: 150px">
                    <legend><% _t('SilvercartPage.REMARKS') %></legend>
                    $CustomHtmlFormFieldByName(Note)
                </fieldset>
            </div>
        </div>
    </div>

    <% if hasOnlyOneStandardAddress %>
        <% control AddressData %>
            <% control SilvercartInvoiceAddress %>
                <% include SilvercartAddressDetailReadOnly %>
            <% end_control %>
        <% end_control %>
    <% else %>
        <div class="subcolumns">
            <div class="c50l">
                <div class="subcl">
                    <% control AddressData %>
                        <% control SilvercartInvoiceAddress %>
                            <% include SilvercartAddressDetailReadOnly %>
                        <% end_control %>
                    <% end_control %>
                </div>
            </div>

            <div class="c50r">
                <div class="subcr">
                    <% control AddressData %>
                        <% control SilvercartShippingAddress %>
                            <% include SilvercartAddressDetailReadOnly %>
                        <% end_control %>
                    <% end_control %>
                </div>
            </div>
        </div>
    <% end_if %>    
    
    $Top.getSilvercartShoppingCartFull
    
    <div class="actionRow">
        <div class="type-button">
            <% control Actions %>
            $Field
            <% end_control %>
        </div>
    </div>
</form>
