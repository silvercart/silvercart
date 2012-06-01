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
        
        <% if isCompanyAddress %>
            <br /><em><% _t('SilvercartCustomer.BUSINESSCUSTOMER') %></em><br />
        <% else %>
            <br /><em><% _t('SilvercartCustomer.REGULARCUSTOMER') %></em><br />
        <% end_if %>

        <% if hasAddressData %>
            <br />
            <% if isCompanyAddress %>
                <div class="silvercart-address-company-section">
                    <% if TaxIdNumber %>$fieldLabel(TaxIdNumber): $TaxIdNumber<br /><% end_if %>
                    <% if Company %>$fieldLabel(Company): $Company<br /><% end_if %>
                </div>
            <% end_if %>
        
            $SalutationText $FirstName $Surname<br/>
            $Street $StreetNumber<br/>
            $Postcode $City<br/>
            $SilvercartCountry.Title<br/>
            <% if Phone %>
                $fieldLabel.PhoneShort: $PhoneAreaCode/$Phone
            <% end_if %>
        <% else %>
            <p class="silvercart-message">
                <% _t('SilvercartAddressHolder.NOT_DEFINED','Not defined yet') %>
            </p>
        <% end_if %>
    </div>
</div>