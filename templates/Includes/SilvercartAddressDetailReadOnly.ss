<div class="silvercart-address">
    <div class="silvercart-address-field_content">
        <% if isInvoiceAndShippingAddress %>
            <strong><% _t('SilvercartAddressHolder.INVOICEANDSHIPPINGADDRESS','invoice and shipping address') %></strong>
        <% else %>
            <% if isInvoiceAddress %>
                <strong><% _t('SilvercartAddressHolder.INVOICEADDRESS','invoice address') %></strong>
            <% end_if %>
            <% if isShippingAddress %>
                <strong><% _t('SilvercartAddressHolder.SHIPPINGADDRESS','shipping address') %></strong>
            <% end_if %>
        <% end_if %>

        <% if isStandardAddress %>
            <br />
            $SalutationText $FirstName $Surname<br/>
            $Street $StreetNumber<br/>
            $Postcode $City<br/>
            $SilvercartCountry.Title<br/>
            <% if Phone %>
                <% _t('SilvercartAddress.PHONE_SHORT','Phone') %>: $PhoneAreaCode/$Phone
            <% end_if %>
        <% else %>
            <p class="silvercart-message">
                <% _t('SilvercartAddressHolder.NOT_DEFINED','Not defined yet') %>
            </p>
        <% end_if %>
    </div>
</div>