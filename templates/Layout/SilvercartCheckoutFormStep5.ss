<form class="yform full" $FormAttributes >
    <% include SilvercartShoppingCartFull %>

    $CustomHtmlFormMetadata
    $CustomHtmlFormErrorMessages

    <div class="subcolumns">
        <div class="c50l">
            <div class="subcl">
                <fieldset>
                    <legend><% _t('SilvercartAddressHolder.SHIPPINGADDRESS') %></legend>
                    <% control AddressData %>
                        <% control SilvercartShippingAddress %>
                            <% include SilvercartAddressTable %>
                        <% end_control %>
                    <% end_control %>
                </fieldset>
            </div>
        </div>

        <div class="c50r">
            <div class="subcr">
                <fieldset>
                    <legend><% _t('SilvercartAddressHolder.INVOICEADDRESS') %></legend>
                    <% control AddressData %>
                        <% control SilvercartInvoiceAddress %>
                            <% include SilvercartAddressTable %>
                        <% end_control %>
                    <% end_control %>
                </fieldset>
            </div>
        </div>
    </div>
    <fieldset>
          <legend><% _t('SilvercartPage.REMARKS') %></legend>
          $CustomHtmlFormFieldByName(Note)
    </fieldset>
    <fieldset>
        <legend><% _t('SilvercartPage.TERMSOFSERVICE_PRIVACY') %></legend>
        $CustomHtmlFormFieldByName(HasAcceptedTermsAndConditions,SilvercartHasAcceptedTermsAndConditionsFieldCheck)
        $CustomHtmlFormFieldByName(HasAcceptedRevocationInstruction,SilvercartHasAcceptedRevocationInstructionFieldCheck)
        $CustomHtmlFormFieldByName(SubscribedToNewsletter,SilvercartHasAcceptedNewsletterFieldCheck)
    </fieldset>
    <div class="actionRow">
        <div class="type-button">
            <% control Actions %>
            $Field
            <% end_control %>
        </div>
    </div>
</form>
