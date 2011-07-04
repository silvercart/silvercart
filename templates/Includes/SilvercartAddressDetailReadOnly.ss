<div class="silvercart-address">
    <% if isInvoiceAddress %>
            <strong><% _t('SilvercartAddressHolder.INVOICEADDRESS','invoice address') %></strong>
    <% end_if %>
    <% if isShippingAddress %>
            <strong><% _t('SilvercartAddressHolder.SHIPPINGADDRESS','shipping address') %></strong>
    <% end_if %>
    <br />
    $SalutaionText $FirstName $Surname<br/>
    $Street $StreetNumber<br/>
    $Postcode $City<br/>
    $SilvercartCountry.Title<br/>
    <% if Phone %>
    <% _t('SilvercartAddress.PHONE_SHORT','Phone') %>: $PhoneAreaCode/$Phone
    <% end_if %>
</div>