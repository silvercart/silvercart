<form class="yform" $FormAttributes >
    $CustomHtmlFormMetadata
    $CustomHtmlFormErrorMessages
    <fieldset>
<% if $InvoiceAddressIsAlwaysShippingAddress %>
        <legend><% _t('SilvercartAddressHolder.INVOICEANDSHIPPINGADDRESS') %></legend>
<% else %>
        <legend><% _t('SilvercartPage.BILLING_ADDRESS','billing address') %></legend>
<% end_if %>
        $CustomHtmlFormFieldByName(InvoiceAddress,SilvercartCustomHtmlFormFieldAddress)
        <div class="silvercart-button m25l">
            <div class="silvercart-button_content">
                <a href="{$CurrentPage.Link}addNewAddress" class="silvercart-trigger-add-address-link"><% _t('SilvercartAddressHolder.ADD','Add new address') %></a>
            </div>
        </div>
    </fieldset>

<% if $InvoiceAddressIsAlwaysShippingAddress %>
        $CustomHtmlFormFieldByName(ShippingAddress,CustomHtmlFormFieldHidden)
        $CustomHtmlFormFieldByName(InvoiceAddressAsShippingAddress, CustomHtmlFormFieldHidden)
<% else %>
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
<% end_if %>

    <div class="actionRow">
        <div class="type-button">
            <% loop Actions %>
            $Field
            <% end_loop %>
        </div>
    </div>
</form>
<% require javascript(silvercart/script/SilvercartAddressHolder.js) %>