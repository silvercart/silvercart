
<div class="silvercart-checkout-address">
    <% if items %>
    <ul>
        <% loop items %>
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
                                <% if isInvoiceAddress && isShippingAddress %>
                                    <div class="silvercart-address-field_content">
                                        <strong><% _t('SilvercartAddressHolder.INVOICEADDRESS','invoice address') %> &amp; <% _t('SilvercartAddressHolder.SHIPPINGADDRESS','shipping address') %></strong>
                                    </div>
                                <% else %>
                                    <% if name == "InvoiceAddress" %>
                                        <% if isInvoiceAddress %>
                                            <div class="silvercart-address-field_content">
                                                <strong><% _t('SilvercartAddressHolder.DEFAULT_INVOICEADDRESS','invoice address') %></strong>
                                            </div>
                                        <% end_if %>
                                    <% end_if %>
                                    <% if name == "ShippingAddress" %>
                                        <% if isShippingAddress %>
                                            <div class="silvercart-address-field_content">
                                                <strong><% _t('SilvercartAddressHolder.DEFAULT_SHIPPINGADDRESS','shipping address') %></strong>
                                            </div>
                                        <% end_if %>
                                    <% end_if %>
                                <% end_if %>
                                <div class="subcolumns">
                                    <div class="c66l">
                                        <div class="silvercart-address-field_content">
                                            <% if IsPackstation %>
                                                <em><% _t('SilvercartAddress.PACKSTATION_LABEL') %></em><br />
                                            <% else_if isCompanyAddress %>
                                                <div class="silvercart-address-company-section">
                                                    <em><% _t('SilvercartCustomer.BUSINESSCUSTOMER') %></em><br />
                                                    <% if TaxIdNumber %>$address.fieldLabel(TaxIdNumber): $TaxIdNumber<br /><% end_if %>
                                                    <% if Company %>$address.fieldLabel(Company): $Company<br /><% end_if %>
                                                </div>
                                            <% else %>
                                                <br />
                                            <% end_if %>
                                            
                                            $SalutationText $FirstName $Surname<br/>
                                            <% if Addition %>
                                                $Addition<br/>
                                            <% end_if %>
                                            <% if IsPackstation %>
                                                $PostNumber<br/>
                                                $Packstation<br/>
                                            <% else %>
                                                $Street $StreetNumber<br/>
                                            <% end_if %>
                                            $Postcode $City<br/>
                                            $SilvercartCountry.Title<br/>
                                            <% if Phone %>
                                                $address.fieldLabel(PhoneShort): $PhoneAreaCode/$Phone
                                            <% end_if %>
                                        </div>
                                    </div>
                                    <div class="c33r">
                                        <a class="silvercart-icon-button edit32" id="silvercart-edit-shipping-address-id" href="{$CurrentPage.Link}editAddress/$ID" title="<% _t('SilvercartAddressHolder.EDIT','edit') %>">
                                            <span class="silvercart-icon-button_content">
                                                &nbsp;
                                            </span>
                                        </a><% if isLastAddress %><% else %><% if Top.canDelete %><a class="silvercart-icon-button delete32" id="silvercart-delete-shipping-address-id" href="{$CurrentPage.Link}deleteAddress/$ID" title="<% _t('SilvercartAddressHolder.DELETE','Delete') %>">
                                            <span class="silvercart-icon-button_content">
                                                &nbsp;
                                            </span>
                                        </a>
                                        <% end_if %><% end_if %>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="c25r">
                    </div>
                </div>
            </li>
        <% end_loop %>
    </ul>
    <% else %>
    <p><% _t('SilvercartAddress.NO_ADDRESS_AVAILABLE') %></p>
    <% end_if %>
</div>
