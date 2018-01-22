<div class="silvercart-address">
    <div class="silvercart-address-field_content">
        <% if $isInvoiceAndShippingAddress %>
            <strong><%t SilvercartAddressHolder.INVOICEANDSHIPPINGADDRESS 'invoice and shipping address' %></strong>
        <% else %>
            <% if $isInvoiceAddress %>
                <strong><%t SilvercartAddressHolder.INVOICEADDRESS 'invoice address' %></strong>
            <% end_if %>
            <% if $isShippingAddress %>
                <strong><%t SilvercartAddressHolder.SHIPPINGADDRESS 'shipping address' %></strong>
            <% end_if %>
        <% end_if %>
        
        <% if $IsPackstation %>
            <br/><em><%t SilvercartAddress.PACKSTATION_LABEL %></em>
        <% else_if $isCompanyAddress %>
            <br /><em><%t SilvercartCustomer.BUSINESSCUSTOMER %></em>
        <% end_if %>

        <% if $hasAddressData %>
            <br />
            <% if $TaxIdNumber || $Company %>
                <div class="silvercart-address-company-section">
                    <% if $TaxIdNumber %>{$fieldLabel(TaxIdNumber)}: {$TaxIdNumber}<br /><% end_if %>
                    <% if $Company %>{$Company}<br /><% end_if %>
                </div>
            <% end_if %>
        
            {$SalutationText} {$AcademicTitle} {$FirstName} {$Surname}<br/>
            <% if $Addition %>
                {$Addition}<br/>
            <% end_if %>
            <% if $IsPackstation %>
                {$PostNumber}<br/>
                {$Packstation}<br/>
            <% else %>
                {$Street} {$StreetNumber}<br/>
            <% end_if %>
            {$Postcode} {$City}<br/>
            {$SilvercartCountry.Title}<br/>
            <% if $Phone %>
                {$fieldLabel(PhoneShort)}: {$PhoneAreaCode} {$Phone}
            <% end_if %>
        <% else %>
        <div class="alert alert-error"><p><%t SilvercartAddressHolder.NOT_DEFINED 'Not defined yet' %></p></div>
        <% end_if %>
    </div>
</div>