<div class="subcolumns">
    <div class="c50l">
        <div id="ShippingInfos">
        <h3><% _t('AddressHolder.SHIPPINGADDRESS','shipping address') %></h3>
        <% if CurrentMember.shippingAddress %>
            <% control CurrentMember.shippingAddress %>
                <% include AddressTable %>
                <a href="/my-account/address-overview/address-details/{$ID}"><% _t('AddressHolder.EDIT','edit') %></a>
            <% end_control %>
        <% else %>
            <p>
                <% sprintf(t_('AddressHolder.EXCUSE_SHIPPINGADDRESS', 'Excuse us %s, but You have not added a delivery address yet.'), "$CurrentMember.FirstName $CurrentMember.Surname") %>
            </p>
        <% end_if %>
        </div>
    </div>
    <div class="c50r">
        <div id="InvoiceInfos">
            <h3><% _t('AddressHolder.INVOICEADDRESS','invoice address') %></h3>
            <% if CurrentMember.invoiceAddress %>
                <% control CurrentMember.invoiceAddress %>
                    <% include AddressTable %>
                        <a href="/my-account/address-overview/address-details/{$ID}"><% _t('AddressHolder.EDIT','edit') %></a>
                    <% end_control %>
            <% else %>
                <p>
                    <% sprintf(t_('AddressHolder.EXCUSE_INVOICEADDRESS', 'Excuse us %s, but You have not added an invoice address yet.'), "$CurrentMember.FirstName $CurrentMember.Surname") %>
                </p>
            <% end_if %>
        </div>
    </div>
</div>