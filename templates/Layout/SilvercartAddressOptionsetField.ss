<div class="silvercart-checkout-address">
    <% if items %>
    <ul class="unstyled">
        <% loop items %>
            <li>
                <div class="row">
                    <div class="span1 silvercart-address-radiofield vertical">
                            <input class="vertical" type="radio" name="$name" value="$value" id="$htmlId"<% if checked %> checked="checked"<% end_if %> />
                    </div>
                    <div class="span4">
                            <label for="$htmlId" class="silvercart-address">
                                <% if isInvoiceAddress && isShippingAddress %>
                                    <div class="silvercart-address-field_content">
                                        <strong><% _t('SilvercartAddressHolder.INVOICEADDRESS','invoice address') %> &amp; <% _t('SilvercartAddressHolder.SHIPPINGADDRESS','shipping address') %></strong>
                                    </div>
                                <% else %>
                                    <% if name = InvoiceAddress %>
                                        <% if isInvoiceAddress %>
                                            <div class="silvercart-address-field_content">
                                                <strong><% _t('SilvercartAddressHolder.DEFAULT_INVOICEADDRESS','invoice address') %></strong>
                                            </div>
                                        <% end_if %>
                                    <% end_if %>
                                    <% if name = ShippingAddress %>
                                        <% if isShippingAddress %>
                                            <div class="silvercart-address-field_content">
                                                <strong><% _t('SilvercartAddressHolder.DEFAULT_SHIPPINGADDRESS','shipping address') %></strong>
                                            </div>
                                        <% end_if %>
                                    <% end_if %>
                                <% end_if %>
                                <div class="row">
                                    <div class="span3">
                                        <div class="silvercart-address-field_content">
                                            <% if IsPackstation %>
                                                <em><% _t('SilvercartAddress.PACKSTATION_LABEL') %></em><br />
                                            <% else_if isCompanyAddress %>
                                                <div class="silvercart-address-company-section">
                                                    <em><% _t('SilvercartCustomer.BUSINESSCUSTOMER') %></em><br />
                                                    <% if TaxIdNumber %>$address.fieldLabel(TaxIdNumber): $TaxIdNumber<br /><% end_if %>
                                                    <% if Company %>$address.fieldLabel(Company): $Company<br /><% end_if %>
                                                </div>
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
                                                $address.fieldLabel(PhoneShort): $PhoneAreaCode $Phone
                                            <% end_if %>
                                        </div>
                                    <% if address.canEdit || address.canDelete %>
                                        <div class="btn-group pull-right">
                                        <% if address.canEdit %>
                                            <a class="btn btn-small edit32" id="silvercart-edit-shipping-address-id" href="{$CurrentPage.Link}editAddress/$ID" title="<% _t('SilvercartAddressHolder.EDIT','edit') %>" data-original-title=""  data-original-title="" data-toggle="tooltip" data-placement="top">
                                                <span class="icon-edit"></span>
                                            </a>
                                        <% end_if %>
                                        <% if isLastAddress %><% else_if address.canDelete %>
                                            <a class="btn btn-small btn-danger delete32" id="silvercart-delete-shipping-address-id" href="{$CurrentPage.Link}deleteAddress/$ID" title="<% _t('SilvercartAddressHolder.DELETE','Delete') %>" data-original-title=""  data-original-title="" data-toggle="tooltip" data-placement="top">
                                                <span class="icon-trash"></span>
                                            </a>
                                        <% end_if %>
                                    </div>
                                    <% end_if %>
                                    </div>

                                </div>
                            </label>
                    </div>
                    <div class="span2">
                    </div>
                </div>
            </li>
        <% end_loop %>
    </ul>
    <% else %>
    <div class="alert alert-error">
    <p><% _t('SilvercartAddress.NO_ADDRESS_AVAILABLE') %></p>
    </div>
    <% end_if %>
</div>
