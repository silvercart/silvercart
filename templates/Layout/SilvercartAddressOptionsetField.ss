
<div class="silvercart-checkout-address">
    <% if items %>
    <ul>
        <% control items %>
            <li>
                <div class="subcolumns equalize">
                    <div class="c20l silvercart-address-radiofield">
                        <div class="subcl">
                            <input type="radio" name="$name" value="$value" id="$htmlId"<% if checked %> checked="checked"<% end_if %> />
                        </div>
                    </div>
                    <div class="c50l">
                        <div class="subcl">
                            <label for="$htmlId" class="silvercart-address">
                                <div class="silvercart-address-field_content">
                                    <% if isInvoiceAddress && isShippingAddress %>
                                                <strong><% _t('SilvercartAddressHolder.INVOICEADDRESS','invoice address') %> &amp; <% _t('SilvercartAddressHolder.SHIPPINGADDRESS','shipping address') %></strong>
                                    <% else %>
                                        <% if name = InvoiceAddress %>
                                            <% if isInvoiceAddress %>
                                                    <strong><% _t('SilvercartAddressHolder.DEFAULT_INVOICEADDRESS','invoice address') %></strong>
                                            <% end_if %>
                                        <% end_if %>
                                        <% if name = ShippingAddress %>
                                            <% if isShippingAddress %>
                                                    <strong><% _t('SilvercartAddressHolder.DEFAULT_SHIPPINGADDRESS','shipping address') %></strong>
                                            <% end_if %>
                                        <% end_if %>
                                    <% end_if %><br/>
                                </div>
                                <div class="subcolumns">
                                    <div class="c66l">
                                        <div class="silvercart-address-field_content">
                                            $SalutaionText $FirstName $Surname<br/>
                                            $Street $StreetNumber<br/>
                                            $Postcode $City<br/>
                                            $SilvercartCountry.Title<br/>
                                            <% if Phone %>
                                                <% _t('SilvercartAddress.PHONE_SHORT','Phone') %>: $PhoneAreaCode/$Phone
                                            <% end_if %>
                                        </div>
                                    </div>
                                    <div class="c33r">
                                        <a class="silvercart-icon-button edit32" id="silvercart-edit-shipping-address-id" href="{$CurrentPage.Link}editAddress/$ID" title="<% _t('SilvercartAddressHolder.EDIT','edit') %>">
                                            <span class="silvercart-icon-button_content">
                                                &nbsp;
                                            </span>
                                        </a><a class="silvercart-icon-button delete32" id="silvercart-delete-shipping-address-id" href="{$CurrentPage.Link}deleteAddress/$ID" title="<% _t('SilvercartAddressHolder.DELETE','Delete') %>">
                                            <span class="silvercart-icon-button_content">
                                                &nbsp;
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="c25r">
                    </div>
                </div>
            </li>
        <% end_control %>
    </ul>
    <% else %>
    <p><% _t('SilvercartAddress.NO_ADDRESS_AVAILABLE') %></p>
    <% end_if %>
</div>
