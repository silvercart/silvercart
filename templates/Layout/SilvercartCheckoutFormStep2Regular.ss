<form class="yform" $FormAttributes >
    $CustomHtmlFormMetadata
    $CustomHtmlFormErrorMessages
    <fieldset>
        <legend><% _t('SilvercartPage.BILLING_ADDRESS','billing address') %></legend>
        $CustomHtmlFormFieldByName(InvoiceAddress,SilvercartCustomHtmlFormFieldAddress)
        <div class="silvercart-button m25l">
            <div class="silvercart-button_content">
                <a href="{$CurrentPage.Link}addNewAddress" class="silvercart-trigger-add-address-link"><% _t('SilvercartAddressHolder.ADD','Add new address') %></a>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend><% _t('SilvercartPage.SHIPPING_ADDRESS','shipping address') %></legend>

        $CustomHtmlFormFieldByName(InvoiceAddressAsShippingAddress, CustomHtmlFormFieldCheck)

        <div id="ShippingAddressFields">
            $CustomHtmlFormFieldByName(ShippingAddress,SilvercartCustomHtmlFormFieldAddress)
            <div class="silvercart-button m25l">
                <div class="silvercart-button_content">
                    <a href="{$CurrentPage.Link}addNewAddress" class="silvercart-trigger-add-address-link"><% _t('SilvercartAddressHolder.ADD','Add new address') %></a>
                </div>
            </div>
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
<% require javascript(silvercart/script/SilvercartAddressHolder.js) %>