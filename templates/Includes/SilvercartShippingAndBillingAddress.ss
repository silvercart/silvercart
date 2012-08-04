<div class="subcolumns">
    <div class="c50l">
        <div class="subcl">
            <h3><% _t('SilvercartAddressHolder.SHIPPINGADDRESS','shipping address') %></h3>
            <% if CurrentMember.SilvercartShippingAddress %>
                <% with CurrentMember.SilvercartShippingAddress %>
                    <% include SilvercartShippingAddressTable %>
                <% endwith %>
                <div class="silvercart-button-row clearfix">
                    <div class="silvercart-button">
                        <div class="silvercart-button_content">
                            <a id="silvercart-edit-shipping-address-id" href="{$PageByIdentifierCode(SilvercartAddressPage).Link}$CurrentMember.SilvercartShippingAddress.ID"><% _t('SilvercartAddressHolder.EDIT','edit') %></a>
                        </div>
                    </div>
                </div>
            <% else %>
                <p>
                    <% _t('SilvercartAddressHolder.EXCUSE_SHIPPINGADDRESS', 'Excuse us, but You have not added a delivery address yet.') %>
                </p>
            <% end_if %>
        </div>
    </div>
    <div class="c50r">
        <div class="subcr">
            <h3><% _t('SilvercartAddressHolder.INVOICEADDRESS','invoice address') %></h3>
            <% if CurrentMember.SilvercartInvoiceAddress %>
                <% with CurrentMember.SilvercartInvoiceAddress %>
                    <% include SilvercartInvoiceAddressTable %>
                <% end_with %>
                <div class="silvercart-button-row clearfix">
                    <div class="silvercart-button">
                        <div class="silvercart-button_content">
                            <a id="silvercart-edit-invoice-address-id" href="{$PageByIdentifierCode(SilvercartAddressPage).Link}$CurrentMember.SilvercartInvoiceAddress.ID">
                                <% _t('SilvercartAddressHolder.EDIT','edit') %>
                            </a>
                        </div>
                    </div>
                </div>
            <% else %>
                <p>
                    <% _t('SilvercartAddressHolder.EXCUSE_INVOICEADDRESS', 'Excuse us, but You have not added an invoice address yet.') %>
                </p>
            <% end_if %>
        </div>
    </div>
</div>
