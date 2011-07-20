<form class="yform full" $FormAttributes >
    $Top.getSilvercartShoppingCartFull

    $CustomHtmlFormMetadata
    $CustomHtmlFormErrorMessages

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
