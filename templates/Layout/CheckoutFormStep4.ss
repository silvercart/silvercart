<form class="yform full" $FormAttributes >

    <% include ShoppingCartFull %>

    $CustomHtmlFormMetadata
    $CustomHtmlFormErrorMessages

    <div class="subcolumns">
        <div class="c50l">
            <div class="subcl">
                <fieldset>
                    <legend><% _t('AddressHolder.SHIPPINGADDRESS') %></legend>
                    <% control AddressData %>
                        <% control shippingAddress %>
                         <% include AddressTable %>
                        <% end_control %>
                    <% end_control %>
                </fieldset>
            </div>
        </div>

        <div class="c50r">
            <div class="subcr">
                <fieldset>
                    <legend><% _t('AddressHolder.INVOICEADDRESS') %></legend>
                    <% control AddressData %>
                        <% control invoiceAddress %>
                            <% include AddressTable %>
                        <% end_control %>
                    <% end_control %>
                </fieldset>
            </div>
        </div>
    </div>

      <fieldset>
          <legend><% _t('Page.REMARKS') %></legend>
          $CustomHtmlFormFieldByName(Note)
    </fieldset>
    <fieldset>
        <legend><% _t('Page.TERMSOFSERVICE_PRIVACY') %></legend>
        $CustomHtmlFormFieldByName(HasAcceptedTermsAndConditions,HasAcceptedTermsAndConditionsFieldCheck)
        $CustomHtmlFormFieldByName(HasAcceptedRevocationInstruction,HasAcceptedRevocationInstructionFieldCheck)
        $CustomHtmlFormFieldByName(SubscribedToNewsletter,CustomHtmlFormFieldCheck)
    </fieldset>
    <div class="actionRow">
        <div class="type-button">
            <% control Actions %>
            $Field
            <% end_control %>
        </div>
    </div>

    $dataFieldByName(SecurityID)
</form>
