<% if SilvercartAddresses %>
    <h2>$Top.Title</h2>
    <% control SilvercartAddresses %>
        <% if Odd %>
    <div class="subcolumns">
        <div class="c50l">
            <div class="subcl">
        <% else %>
        <div class="c50r">
            <div class="subcr">
        <% end_if %>
                <div class="silvercart-address">
        <% if isInvoiceAddress && isShippingAddress %>
                    <strong><% _t('SilvercartAddressHolder.INVOICEADDRESS','invoice address') %> &amp; <% _t('SilvercartAddressHolder.SHIPPINGADDRESS','shipping address') %></strong>
        <% else %>
            <% if isInvoiceAddress %>
                    <strong><% _t('SilvercartAddressHolder.INVOICEADDRESS','invoice address') %></strong>
            <% end_if %>
            <% if isShippingAddress %>
                    <strong><% _t('SilvercartAddressHolder.SHIPPINGADDRESS','shipping address') %></strong>
            <% end_if %>
        <% end_if %><br/>
                    <div class="subcolumns">
                        <div class="c66l">
                            $SalutaionText $FirstName $Surname<br/>
                            $Street $StreetNumber<br/>
                            $Postcode $City<br/>
                            $SilvercartCountry.Title<br/>
                            <% if Phone %>
                            <% _t('SilvercartAddress.PHONE_SHORT','Phone') %>: $PhoneAreaCode/$Phone
                            <% end_if %>
                        </div>
                        <div class="c33r">
                            <a class="silvercart-icon-button edit32" id="silvercart-edit-shipping-address-id" href="$Top.PageByIdentifierCodeLink(SilvercartAddressPage)$ID" title="<% _t('SilvercartAddressHolder.EDIT','edit') %>">
                                &nbsp;
                            </a>
                            <a class="silvercart-icon-button delete32" id="silvercart-delete-shipping-address-id" href="{$Top.Link}deleteAddress/$ID" title="<% _t('SilvercartAddressHolder.DELETE','Delete') %>">
                                &nbsp;
                            </a>
                        </div>
                    </div>
                    <div class="subcolumns silvercart-address-bottom">
                        <div class="c50l">
                            <div class="subcl">
        <% if isInvoiceAddress %>
                                <p class="silvercart-message info32"><% _t('SilvercartAddressHolder.DEFAULT_INVOICE','This is your invoice address') %></p>
        <% else %>
                                <a class="silvercart-icon-with-text-button p set-as32" href="{$Top.Link}setInvoiceAddress/$ID"><% _t('SilvercartAddressHolder.SET_DEFAULT_INVOICE','Set as invoice address') %></a>
        <% end_if %>
                            </div>
                        </div>
                        <div class="c50r">
                            <div class="subcr">
        <% if isShippingAddress %>
                                <p class="silvercart-message info32"><% _t('SilvercartAddressHolder.DEFAULT_SHIPPING','This is your shipping address') %></p>
        <% else %>
                                <a class="silvercart-icon-with-text-button p set-as32" href="{$Top.Link}setShippingAddress/$ID"><% _t('SilvercartAddressHolder.SET_DEFAULT_SHIPPING','Set as shipping address') %></a>
        <% end_if %>
                            </div>
                        </div>
                    </div>
                </div>
        <% if Odd %>
            </div>
        </div>
            <% if Last %>
    </div>
            <% end_if %>
        <% else %>
            </div>
        </div>
    </div>
        <% end_if %>
    <% end_control %>
<% end_if %>