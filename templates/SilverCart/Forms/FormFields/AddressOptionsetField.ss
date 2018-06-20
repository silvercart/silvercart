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
                            <strong><%t SilverCart\Model\Pages\AddressHolder.INVOICEADDRESS 'invoice address' %> &amp; <%t SilverCart\Model\Pages\AddressHolder.SHIPPINGADDRESS 'shipping address' %></strong>
                        </div>
                    <% else %>
                        <% if $name == InvoiceAddress %>
                            <% if $isInvoiceAddress %>
                                <div class="silvercart-address-field_content">
                                    <strong><%t SilverCart\Model\Pages\AddressHolder.DEFAULT_INVOICEADDRESS 'invoice address' %></strong>
                                </div>
                            <% end_if %>
                        <% end_if %>
                        <% if $name == ShippingAddress %>
                            <% if $isShippingAddress %>
                                <div class="silvercart-address-field_content">
                                    <strong><%t SilverCart\Model\Pages\AddressHolder.DEFAULT_SHIPPINGADDRESS 'shipping address' %></strong>
                                </div>
                            <% end_if %>
                        <% end_if %>
                    <% end_if %>
                        <div class="row">
                            <div class="span3">
                                <div class="silvercart-address-field_content">
                                    <% if $IsPackstation == 1 %>
                                        <em>{$address.fieldLabel('PackstationLabel')}</em><br />
                                    <% else_if $TaxIdNumber || $Company %>
                                        <div class="silvercart-address-company-section">
                                            <% if $isCompanyAddress %><em><%t SilverCart\Model\Customer\Customer.BUSINESSCUSTOMER 'Business customer' %></em><br /><% end_if %>
                                            <% if $TaxIdNumber %>{$address.fieldLabel(TaxIdNumber)}: {$TaxIdNumber}<br /><% end_if %>
                                            <% if $Company %>{$Company}<br /><% end_if %>
                                        </div>
                                    <% end_if %>

                                    <% if $FirstName || $Surname %>
                                        {$SalutationText} {$AcademicTitle} {$FirstName} {$Surname}<br/>
                                    <% end_if %>
                                    <% if $Addition %>
                                        {$Addition}<br/>
                                    <% end_if %>
                                    <% if $IsPackstation == 1 %>
                                        {$PostNumber}<br/>
                                        {$Packstation}<br/>
                                    <% else %>
                                        {$Street} {$StreetNumber}<br/>
                                    <% end_if %>
                                    {$Postcode} {$City}<br/>
                                    {$Country.Title}<br/>
                                    <% if $Phone %>
                                        {$address.fieldLabel(PhoneShort)}: {$Phone}
                                    <% end_if %>
                                </div>
                            <% if $address.canEdit || $address.canDelete %>
                                <div class="btn-group pull-right">
                                <% if address.canEdit %>
                                    <a class="btn btn-small edit32" id="silvercart-edit-shipping-address-id" href="{$CurrentPage.Link}editAddress/$ID" title="<%t SilverCart\Model\Pages\AddressHolder.EDIT 'edit' %>" data-original-title=""  data-original-title="" data-toggle="tooltip" data-placement="top">
                                        <span class="icon-edit"></span>
                                    </a>
                                <% end_if %>
                                <% if isLastAddress %><% else_if address.canDelete %>
                                    <a class="btn btn-small btn-danger delete32" id="silvercart-delete-shipping-address-id" href="{$CurrentPage.Link}deleteAddress/$ID" title="<%t SilverCart\Model\Pages\AddressHolder.DELETE 'Delete' %>" data-original-title=""  data-original-title="" data-toggle="tooltip" data-placement="top">
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
    <div class="alert alert-error"><p>{$fieldLabel('NoAddressAvailable')}</p></div>
<% end_if %>
</div>
