<div class="subcolumns">
    <div class="c50l">
        <div id="ShippingInfos">
        <h3><% _t('SilvercartAddressHolder.SHIPPINGADDRESS','shipping address') %></h3>
        <% if CurrentMember.SilvercartShippingAddress %>
            <% control CurrentMember.SilvercartShippingAddress %>
                <% include SilvercartAddressTable %>
                <a href="/my-account/address-overview/address-details/{$ID}"><% _t('SilvercartAddressHolder.EDIT','edit') %></a>
            <% end_control %>
        <% else %>
            <p>
                <% sprintf(t_('SilvercartAddressHolder.EXCUSE_SHIPPINGADDRESS', 'Excuse us %s, but You have not added a delivery address yet.'), "$CurrentMember.FirstName $CurrentMember.Surname") %>
            </p>
        <% end_if %>
        </div>
    </div>
    <div class="c50r">
        <div id="InvoiceInfos">
            <h3><% _t('SilvercartAddressHolder.INVOICEADDRESS','invoice address') %></h3>
            <% if CurrentMember.SilvercartInvoiceAddress %>
                <% control CurrentMember.SilvercartInvoiceAddress %>
                    <% include SilvercartAddressTable %>
                        <a href="/my-account/address-overview/address-details/{$ID}"><% _t('SilvercartAddressHolder.EDIT','edit') %></a>
                    <% end_control %>
            <% else %>
                <p>
                    <% sprintf(t_('SilvercartAddressHolder.EXCUSE_INVOICEADDRESS', 'Excuse us %s, but You have not added an invoice address yet.'), "$CurrentMember.FirstName $CurrentMember.Surname") %>
                </p>
            <% end_if %>
        </div>
    </div>
</div>
