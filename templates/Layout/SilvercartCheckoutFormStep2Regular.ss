<form class="yform" $FormAttributes >
    $CustomHtmlFormMetadata
    $CustomHtmlFormErrorMessages
    <fieldset>
        <legend><% _t('SilvercartPage.BILLING_ADDRESS','billing address') %></legend>
        $CustomHtmlFormFieldByName(InvoiceAddress,SilvercartCustomHtmlFormFieldAddress)
    </fieldset>

    <fieldset>
        <legend><% _t('SilvercartPage.SHIPPING_ADDRESS','shipping address') %></legend>

        $CustomHtmlFormFieldByName(InvoiceAddressAsShippingAddress, CustomHtmlFormFieldCheck)

        <div id="ShippingAddressFields">
            $CustomHtmlFormFieldByName(ShippingAddress,SilvercartCustomHtmlFormFieldAddress)
        </div>
    </fieldset>

    <div class="actionRow">
        <div class="type-button">
            <% loop Actions %>
            $Field
            <% end_loop %>
        </div>
    </div>
</form>