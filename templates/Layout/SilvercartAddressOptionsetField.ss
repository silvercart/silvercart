<div class="silvercart-checkout-address">
<% if $items %>
    <ul class="unstyled">
    <% loop $items %>
        <li>
            <div class="row">
                <div class="span1 silvercart-address-radiofield vertical">
                    <input class="vertical" type="radio" name="{$name}" value="{$value}" id="{$htmlId}"<% if $checked %> checked="checked"<% end_if %> />
                </div>
                <div class="span4">
                    <label for="{$htmlId}" class="silvercart-address">
                        <% if $isInvoiceAddress && $isShippingAddress %>
                            <div class="silvercart-address-field_content">
                                <strong><%t SilvercartAddressHolder.INVOICEADDRESS 'invoice address' %> &amp; <%t SilvercartAddressHolder.SHIPPINGADDRESS 'shipping address' %></strong>
                            </div>
                        <% else_if $name == 'InvoiceAddress' && $isInvoiceAddress %>
                            <div class="silvercart-address-field_content">
                                <strong><%t SilvercartAddressHolder.DEFAULT_INVOICEADDRESS 'invoice address' %></strong>
                            </div>
                        <% else_if $name == 'ShippingAddress' && isShippingAddress %>
                            <div class="silvercart-address-field_content">
                                <strong><%t SilvercartAddressHolder.DEFAULT_SHIPPINGADDRESS 'shipping address' %></strong>
                            </div>
                        <% end_if %>
                        <div class="row">
                            <div class="span3">
                                <div class="silvercart-address-field_content">
                                <% if $IsPackstation %>
                                    <em><%t SilvercartAddress.PACKSTATION_LABEL %></em><br />
                                <% else_if $TaxIdNumber || $Company %>
                                    <div class="silvercart-address-company-section">
                                        <% if $isCompanyAddress %><em><%t SilvercartCustomer.BUSINESSCUSTOMER %></em><br /><% end_if %>
                                        <% if $TaxIdNumber %>{$address.fieldLabel(TaxIdNumber)}: {$TaxIdNumber}<br /><% end_if %>
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
                                    {$address.fieldLabel(PhoneShort)}: {$PhoneAreaCode} {$Phone}
                                <% end_if %>
                                </div>
                            <% if $address.canEdit || $address.canDelete %>
                                <div class="btn-group pull-right">
                                <% if $address.canEdit %>
                                    <a class="btn btn-small" id="silvercart-edit-shipping-address-id" href="{$CurrentPage.Link}editAddress/{$ID}" title="<%t SilvercartAddressHolder.EDIT 'edit' %>" data-toggle="tooltip" data-placement="top"><span class="icon-edit"></span></a>
                                <% end_if %>
                                <% if not $isLastAddress && $address.canDelete %>
                                    <a class="btn btn-small btn-danger" id="silvercart-delete-shipping-address-id" href="{$CurrentPage.Link}deleteAddress/{$ID}" title="<%t SilvercartAddressHolder.DELETE 'Delete' %>" data-toggle="tooltip" data-placement="top"><span class="icon-trash"></span></a>
                                <% end_if %>
                            </div>
                            <% end_if %>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </li>
    <% end_loop %>
    </ul>
<% else %>
    <div class="alert alert-error">
    <p><%t SilvercartAddress.NO_ADDRESS_AVAILABLE %></p>
    </div>
<% end_if %>
</div>
